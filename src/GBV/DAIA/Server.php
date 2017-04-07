<?php declare(strict_types=1);

namespace GBV\DAIA;

use DAIA\Request;
use DAIA\Response;
use DAIA\Error;
use DAIA\Document;
use DAIA\Item;
use DAIA\Limitation;
use DAIA\ServiceStatus;
use DAIA\Available;
use DAIA\Unavailable;

use GBV\DocumentID;
use GBV\DAIA\Record;

use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Monolog\Logger;

/** @package GBVDAIA */
class Server extends \DAIA\Server
{
    use \Psr\Log\LoggerAwareTrait;

    protected $config;
    protected $client;

    public $isil;

    public function __construct(Config $config, LoggerInterface $logger=null)
    {
        $this->setLogger($logger ?? new Logger());
        $this->config = $config;

        // configure HTTP request logging
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->unshift(\GuzzleHttp\Middleware::log($logger,
            new \GuzzleHttp\MessageFormatter("{method} {uri} {code}"),
            Logger::INFO    // internal HTTP request: INFO
        ));
        $stack->unshift(\GuzzleHttp\Middleware::log($logger,
            new \GuzzleHttp\MessageFormatter("{res_headers}\n{res_body}"),
            Logger::DEBUG   // internal HTTP response: DEBUG
        ));

        // setup HTTP client
        $this->client = new Client([
            'connect_timeout' => 1.0,
            'timeout' => 3.0,
            'User-Agent' => 'GBVDAIA/0.0.0',
            'handler' => $stack,
        ]);
    }

    /**
     * @throws \DAIA\Error
     */
    public function queryHandler(Request $request): Response
    {
        // DAIA Request object: INFO
        # $this->logger->info('request', ['request'=>$request, 'isil' => $isil]);

        $response = new Response();

        if ($this->isil) {
            $uri = "http://uri.gbv.de/organization/isil/".$this->isil;

            # TODO: handle request error
            # TODO: use lobid.org instead
            $data = $this->getJSON("$uri?format=json");
            if ($data) {
                $this->logger->notice('ORG: {data}',['data'=>json_encode($data[$uri])]);
                try {
                    $homepage = $data[$uri]["http://xmlns.com/foaf/0.1/homepage"][0]['value'];
                    // TODO: use long name instead (shortname & long name)
                    $name =  $data[$uri]["http://xmlns.com/foaf/0.1/name"][0]['value'];
                } catch (\Exception $e) {}
                $response->institution = new \DAIA\Institution([
                    'id' => $uri
                ]);
                if (isset($homepage)) $response->institution->href = $homepage;
                if (isset($name)) $response->institution->content = $name;
            }
        }
    
        if (!count($request->ids)) {
            $this->logger->notice('missing request identifier');
            return $response;
        }

        # TODO: handle multiple request IDs

        $id = DocumentID::parse($request->ids[0], $this->isil);
        if ($id) {
            try {
                $doc = $this->queryDocument($id);
            # TODO: catch 404
            } catch (RequestException $e) {
                $this->logger->error("502");
                throw new Error(502, 'internal request failed');
            }
        }
        if ($doc) {
            $this->logger->debug('{document}',['document'=>$doc]);

            $response->addDocument($doc);

            $this->logger->debug('{document}',['document'=>$doc]);
        }
        
        $response->language = 'de';

        return $response;
    }
    
    protected function exceptionHandler(Request $request, \Throwable $exception)
    {
        $this->logger->critical('Unexpected error', [
            'request' => $request,
            'server' => $this,
            'exception' => $exception,
        ]);
        return true;
    }

    protected function getJSON($url) {
        $response = $this->client->get($url);
        if ($response->getStatusCode() == 200) {
            return json_decode((string)$response->getBody(), True);
        } else {
            return [];
        }
    }

    public function queryDocument(DocumentID $id)
    {
        // fetch PICA record
        $response = $this->client->get('http://unapi.gbv.de/', [
            'query'=> ['id' => $id->short(), 'format' => 'pp'],
            'http_errors' => false,
        ]);
        # TODO: catch 404, this is fine if not found!
        # TODO: don't throw HTTP errors but check back

        if ($response->getStatusCode() == 404) {
            return;
        } elseif ($response->getStatusCode() != 200) {
            # TODO: throw error (502)
            return;
        }
        
        $pica = (string) $response->getBody();

        // document found
        $doc = new Document(['id'=>$id->uri(), 'requested'=>$id->requested]);
        # TODO: add href based on opac URL
        # TODO: add department
        
        # TODO: catch parsing error (e.g. empty string)
        $pica = new Record($pica);


        foreach ($pica->holdings as $iln => $holdings) {
            foreach ($holdings as $holding) {
                if ($holding->epn) {
                    $doc->addItem($this->convertHolding($id, $holding));
                } else {
					$this->logger->warn('holding without epn', (array)$holding);
				}
            }
        }

        return $doc;
    }

    public function convertHolding(DocumentID $id, Holding $holding)
    {
		$this->logger->debug('convertHolding', (array)$holding);

        $item = new Item([
			'id' => "http://uri.gbv.de/document/{$id->dbkey}:epn:{$holding->epn}",
			'label' => $holding->label,			
		]);

		$queue = (int)$holding->queue;
        if ((string)$queue === $holding->queue) {
            $item->queue = $queue;
        }

        // TODO: href, part, chronology, department, storage

        // Bandliste: Keine DAIA services auswerten
        if ($holding->status == '6') {
            return $item;
        }

        $indicator = $holding->indikator ?? '';

		/**
		 * TODO: in ausleihindikator.yaml auslagern
		# Katalogspezifischer Standardwert falls nicht gesetzt
		# TODO: Dies sollte in eine Konfigurationsdatei ausgelagert werden!
		# Siehe https://info.gbv.de/display/DiB/DAIA (FIXME)
		if ($d eq "" and $iln) {
			if (grep { $_ eq $iln } qw(21 24 26 31 34 39 41 42 44 45 47 49 56 59 85 89 91 122 132 134 151 158 159 163 164 166 213)) {
				$d = 'g';
			} elsif (grep { $_ eq $iln } qw(43 82 97 209 229)) {
				$d = 's';
			}
		}
		*
		*/


        $services = $this->config->loanIndicator($id->dbkey, $indicator);
        foreach ($services as $name => $config) {
            if (!preg_match('/(^presentation|loan|interloan)$/', $name)) {
                continue;
            }
            $service = $this->holdingService($holding, $name, $config);
            if ($service instanceof Available) {
                $item->addAvailable($service);
            } elseif ($service instanceof Unavailable) {
                $item->addUnavailable($service);
            }
        }
        
        return $item;
    }

    public function holdingService(Holding $holding, $service, $config): ServiceStatus
    {
        $this->logger->debug('holdingService', ['service'=>$service, 'config' => $config]);

        $is = $config['is'] ?? 'unavailable';
        $has = [ 'service' => $service ];

		// limitation
        $limitation = $config['limitation'] ?? null;
		if ($limitation) {
			$has['limitation'] = [new Limitation(['content'=>$limitation])];
		}

        // expected
        $expected = $config['expected'] ?? $holding->date ?? null;
        if ($expected && preg_match('/^(\d\d)-(\d\d)-(20\d\d)$/', $expected, $match)) {
            $expected = $match[1].$match[2].$match[3];
        }

		// status
        if ($holding->status == 1 and $holding->href) {
            // verfügbar, muss bestellt werden
            if ($is == 'available') {
                $has['href'] = $holding->href;
                $has['delay'] = 'unknown';
            }
        } elseif ($holding->status !== '0') {
            // derzeit ausgeliehen oder muss bestellt werden
            $is = 'unavailable';
            $has['href'] = $holding->href;
            if (!$expected) {
                $expected = 'unknown';
            }
        }

        $has['expected'] = $expected;

        return $is == 'available' ? new Available($has) : new Unavailable($has);
    }
}
