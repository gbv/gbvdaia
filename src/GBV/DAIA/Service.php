<?php
declare(strict_types=1);

namespace GBV\DAIA;

use DAIA\Response;
use DAIA\Error;
use DAIA\Document;
use DAIA\Item;
use DAIA\Entity;
use DAIA\ServiceStatus;
use DAIA\Available;
use DAIA\Unavailable;

use GBV\DocumentID;
use GBV\ISIL;
use GBV\DAIA\Record;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Monolog\Logger;

class Service
{
    protected $config;
    protected $log;
    protected $client;


    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->log = $config->logger();

        // configure HTTP logging
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->unshift(\GuzzleHttp\Middleware::log($this->log,
            new \GuzzleHttp\MessageFormatter("{method} {uri} {code}"),
            Logger::INFO    // internal HTTP request: INFO
        ));
        $stack->unshift(\GuzzleHttp\Middleware::log($this->log,
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


    public static function isilFromPath(string $path)
    {
        if (preg_match('!^/isil/(.+)!', $path, $match) and ISIL::ok($match[1])) {
            return $match[1];
        }
    }

    public function query($request, $isil=null): Response
    {
        // DAIA Request object: INFO
        # $this->log->info('request', ['request'=>$request, 'isil' => $isil]);


        $response = new Response();

        if ($isil) {
            $uri = "http://uri.gbv.de/organization/isil/$isil";
            # TODO: check whether Org exists and get Organization name
            #$xml = $this->client->get('http://unapi.gbv.de/', [
            $response->organization = new Entity(['uri'=>$uri]);
        }
    
        if (!count($request->ids)) {
            $this->log->notice('missing request identifier');
            return $response;
        }

        # TODO: handle multiple request IDs

        $id = DocumentID::parse($request->ids[0], $isil);
        if ($id) {
            try {
                $doc = $this->queryDocument($id);
            # TODO: catch 404
            } catch (RequestException $e) {
                $this->log->error("502");
                throw new Error(502, 'internal request failed');
            }
        }
        if ($doc) {
            $response->document[] = $doc;
        }
        
        $response->setLanguage('de');

        return $response;
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
        $doc = new Document($id->uri(), $id->requested);
        # TODO: add href based on opac URL
        # TODO: add department
        
        # TODO: catch parsing error (e.g. empty string)
        $pica = new Record($pica);


        foreach ($pica->holdings as $iln => $holdings) {
            foreach ($holdings as $holding) {
                if ($holding->epn) {
                    $doc->item[] = $this->convertHolding($id, $holding);
                } else {
					$this->log->warn('holding without epn', (array)$holding);
				}
            }
        }

        return $doc;
    }

    public function convertHolding(DocumentID $id, Holding $holding)
    {
		$this->log->debug('convertHolding', (array)$holding);

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
                $item->available[] = $service;
            } elseif ($service instanceof Unavailable) {
                $item->unavailable[] = $service;
            }
        }
        
        return $item;
    }

    public function holdingService(Holding $holding, $service, $config): ServiceStatus
    {
        $this->log->debug('holdingService', ['service'=>$service, 'config' => $config]);

        $is = $config['is'] ?? 'unavailable';
        $has = [ 'service' => $service ];

		// limitation
        $limitation = $config['limitation'] ?? null;
		if ($limitation) {
			$has['limitation'] = [new Entity(['content'=>$limitation])];
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
