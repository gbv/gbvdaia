<?php
declare(strict_types=1);

namespace GBV\DAIA;

use DAIA\Response;
use DAIA\Error;
use DAIA\Document;
use DAIA\Item;
use DAIA\Entity;

use GBV\DocumentID;
use GBV\ISIL;
use GBV\DAIA\Record;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Monolog\Logger;

class Service
{
    protected $config;
    protected $logger;
    protected $client;


    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->logger = $config->logger();

        // configure HTTP logging
        $stack = \GuzzleHttp\HandlerStack::create();
        $stack->unshift(\GuzzleHttp\Middleware::log($this->logger,
            new \GuzzleHttp\MessageFormatter("{method} {uri} {code}"),
            Logger::INFO    // internal HTTP request: INFO
        ));
        $stack->unshift(\GuzzleHttp\Middleware::log($this->logger,
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
        # $this->logger->info('request', ['request'=>$request, 'isil' => $isil]);


        $response = new Response();

        if ($isil) {
            $uri = "http://uri.gbv.de/organization/isil/$isil";
            # TODO: check whether Org exists and get Organization name
            #$xml = $this->client->get('http://unapi.gbv.de/', [
            $response->organization = new Entity(['uri'=>$uri]);
        }
    
        if (!count($request->ids)) {
            $this->logger->notice('missing request identifier');
            return $response;
        }

        # TODO: handle multiple request IDs

        $id = DocumentID::parse($request->ids[0], $isil);
        if ($id) {
            try {
                $doc = $this->queryDocument($id);
            # TODO: catch 404
            } catch (RequestException $e) {
                $this->logger->error("502");
                throw new Error(502, 'bad_gateway', 'internal request failed');
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


        # TODO: make sure that all holdings have epn not null
        foreach ($pica->holdings as $iln => $holdings) {
            foreach ($holdings as $holding) {
                $doc->item[] = $this->convertHolding($id, $holding);
            }
        }

        return $doc;
    }

    public function convertHolding(DocumentID $id, Holding $holding)
    {
        $item = new Item();
        $item->id = "http://uri.gbv.de/document/{$id->dbkey}:epn:{$holding->epn}";
        if ($holding->label) {
            $item->label = $holding->label;
        }
        if ($holding->queue) {
            $item->queue = $holding->queue;
        }

        # TODO: status, sst, indikator, href
        
        return $item;
    }
}
