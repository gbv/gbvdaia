<?php
namespace DAIA;

class DAIAResponseTest extends \PHPUnit\Framework\TestCase
{
    public function testDAIAResponse()
    {
        $res = new DAIAResponse();

        $body = '{"document":\[\],"timestamp":".+"}';
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

    public function testResponseData() {
        $data = [
            'document' => [["id"=>"i:d"]],
            'institution' => [ 'content' => 'library' ],
            'department' => 'ignored'
        ];
        $res = new DAIAResponse($data);
        $expect = new DAIAResponse();
        $expect->document = [ new Document(['id'=>'i:d']) ];
        $expect->institution = new Institution(['content'=>'library']);
        $this->assertEquals($res, $expect);

        $t = $res->timestamp;
        $res->timestamp = '123';
        $this->assertEquals($res->timestamp, $t);
    }
}
