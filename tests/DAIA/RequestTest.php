<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;
use DAIA\Request;

class RequestTest extends TestCase
{
    public function testIds()
    {
        $r = new Request();
        $this->assertSame($r->ids, []);
        $this->assertSame($r->method, 'GET');
        
        $r = new Request(['id'=>'']);
        $this->assertSame($r->ids, []);

        $r = new Request('0');
        $this->assertSame($r->ids, ['0']);

        $r = new Request(['id'=>'foo||0']);
        $this->assertSame($r->ids, ['foo','0']);
    }

    public function testPsr7() {
        if (class_exists('GuzzleHttp\Psr7\ServerRequest')) {
            $serverRequest = new \GuzzleHttp\Psr7\ServerRequest('GET','http://example.org/');

            $req = $serverRequest->withQueryParams(['id'=>'0']);

            $expect = new Request('0');
            $this->assertEquals(Request::fromPsr7($req), $expect);

            $req = new \GuzzleHttp\Psr7\ServerRequest('OPTIONS','http://example.org/');
            $expect = new Request();
            $expect->method = 'OPTIONS';
            $this->assertEquals(Request::fromPsr7($req), $expect);
        }
    }

    /**
     * @expectedException TypeError
     * @expectedExceptionMessage must be an instance of Psr\Http\Message\ServerRequestInterface
     */
    public function testPsr7Error() {
        if (interface_exists('Psr\Http\Message\ServerRequestInterface')) {
            Request::fromPsr7('hello'); 
        }
    }
}
