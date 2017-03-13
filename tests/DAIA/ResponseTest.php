<?php
namespace DAIA;

class ResponseTest extends \PHPUnit\Framework\TestCase
{
    public function testResponse()
    {
        $res = new Response();

        $body = '{.+}';
        $this->assertRegExp("/^$body$/", "$res");
        $this->assertSame(200, $res->getStatusCode());
        $this->assertRegExp("/^\/\*\*\/foo\($body\);$/", $res->getBody('foo'));
        
        $this->assertSame($res->getHeaders(), [
			'Access-Control-Allow-Origin' => ['*'],
			'Content-Language' => ['en'],
			'Content-Type' => ['application/json; charset=utf-8'],
			'X-DAIA-Version' => ['1.0.0'],
        ]);

        $this->assertSame($res->getHeaders('foo')['Content-Type'], ['application/javascript']);
    }
}
