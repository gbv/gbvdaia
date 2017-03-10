<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testResponse()
    {
        $res = new Response();

        $body = '{.+}';
        $this->assertRegExp("/^$body$/", "$res");
        $this->assertSame(200, $res->getStatusCode());
        $this->assertRegExp("/^\/\*\*\/foo\($body\);$/", $res->getBody('foo'));
        
        $headers = $res->getHeaders();
        ksort($headers);
        $this->assertSame($headers, [
			'Access-Control-Allow-Origin' => ['*'],
			'Content-Language' => ['en'],
			'Content-Type' => ['application/json; charset=utf-8'],
			'X-DAIA-Version' => ['1.0.0'],
        ]);
    }
}
