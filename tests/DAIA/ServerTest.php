<?php
namespace DAIA;

class ServerTest extends \PHPUnit\Framework\TestCase
{
    private $server;

    public function setUp()
    {        
        $this->server = new class extends Server {
            public $error;
			public function queryHandler(Request $request): Response {
                if (count($request->ids)>1) {
                    1 % 0;
                }
				return new Response();
			}
            public function exceptionHandler(Request $request, \Throwable $exception) {
                $this->error = [
                    'request' => $request,
                    'server' => $this,
                    'exception' => $exception
                ];
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

    public function testUnexpectedServerError()        
    {
        $req = Request::fromGlobals([], ['id'=>'x:1|x:2']);
        $res = $this->server->query($req);

        $this->assertSame(500, $res->getStatusCode());
        $error = $this->server->error;
        $this->assertSame($error['server'], $this->server);
        $this->assertSame($error['request'], $req);
        $this->assertInstanceOf('DivisionByZeroError', $error['exception']);
    }
} 
