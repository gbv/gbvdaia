<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;

class OptionsResponseTest extends TestCase
{
    public function testOptionsResponse()
    {
        $res = new OptionsResponse();

        $this->assertSame($res->getStatusCode(), 200);
        $this->assertSame("$res", "{}");
        $this->assertSame($res->getBody(), '{}');

        $headers = $res->getHeaders();
        ksort($headers);
        $this->assertSame($headers, [
			'Access-Control-Allow-Headers' => ['Authorization, Content-Type'],
			'Access-Control-Allow-Methods' => ['GET, HEAD, OPTIONS'],
			'Access-Control-Allow-Origin' => ['*'],
			'Content-Language' => ['en'],
			'Content-Type' => ['application/json; charset=utf-8'],
			'X-DAIA-Version' => ['1.0.0'],
        ]);
    }
}
