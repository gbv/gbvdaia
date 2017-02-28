<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    public function testError()
    {
        $e = new Error(404, "not_found");
        $json = '{"code":404,"error":"not_found"}';
        $this->assertSame("$e", $json);

        $this->assertSame($e->getStatusCode(), 404);

        // JSONP response body
        $this->assertSame($e->getBody('foo'), "/**/foo($json);");

        // pretty-printed response body
        $json = "{\n    \"code\": 404,\n    \"error\": \"not_found\"\n}";
        $this->assertSame($e->getBody(), $json);
    }
}
