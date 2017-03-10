<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;

class ErrorResponseTest extends TestCase
{
    public function testErrorResponse()
    {
        $res = new ErrorResponse(404);

        $json = '{"code":404,"error":"not_found"}';
        $this->assertSame("$res", $json);

        $this->assertSame($res->getStatusCode(), 404);
        $this->assertSame($res->getBody('foo'), "/**/foo($json);");
        
        $json = "{\n    \"code\": 404,\n    \"error\": \"not_found\"\n}";
        $this->assertSame($res->getBody(), $json);

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
