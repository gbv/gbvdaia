<?php
namespace DAIA;

class ServerTest extends \PHPUnit\Framework\TestCase
{
    private $server;

    public function setUp()
    {        
        $this->server = new class extends Server {
			public function queryImplementation(Request $request): Response {
				return new Response();
			}
		};
    }

    public function testOptionsRequest()
    {
        $req = Request::fromGlobals(['REQUEST_METHOD'=>'OPTIONS'], []);
        $res = $this->server->query($req);

        $this->assertSame(200, $res->getStatusCode());
        $this->assertSame($res->getHeaders(), [
 			'Access-Control-Allow-Headers' => ['Authorization, Content-Type'],
    		'Access-Control-Allow-Methods' => ['GET, HEAD, OPTIONS'],
			'Access-Control-Allow-Origin' => ['*'],
			'Content-Language' => ['en'],
			'Content-Type' => ['application/json; charset=utf-8'],
			'X-DAIA-Version' => ['1.0.0'],
        ]);
	}

    public function testUnexpectedHTTPVerbRequest()
    {
        $req = Request::fromGlobals(['REQUEST_METHOD'=>'PUT'], []);
        $res = $this->server->query($req);

        $this->assertSame(405, $res->getStatusCode());
    }
} 
