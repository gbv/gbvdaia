<?php declare(strict_types=1);

namespace DAIA;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;

/**
 * Wraps another DAIA server accessible via HTTP.
 */
class Proxy extends Server
{
    /** @var string base URL of the DATA server to wrap */    
    protected $base;

    /** @var HTTP client */
    protected $client;
    protected $requestFactory;

    public function __construct(string $base, HTTPClient $client=null)
    {
        $this->client = $client ?? HttpClientDiscovery::find();
        $this->requestFactory = MessageFactoryDiscovery::find();
        $this->uriFactory = UriFactoryDiscovery::find();
        $this->base = $this->uriFactory->createUri($base);
    }

    public function queryHandler(Request $request): Response
    {        
        // TODO: handle multiple ids
        $id = $request->ids[0] ?? '';

        $request = $this->requestFactory->createRequest(
            'GET',
            $this->base->withQuery("format=json&id=$id"), # FIXME: encoding of id?
            [] // headers
        );

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() == 200) {
            $data = json_decode((string)$response->getBody(), true);
            return new Response($data);
        } else {
            return ErrorResponse(); # TODO: add details
        }
    }
}
