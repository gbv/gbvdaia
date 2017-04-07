<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;
use DAIA\Request;

class RequestTest extends TestCase
{
    public function request(array $query=[])
    {
        return Request::fromGlobals(['HTTP_REQUEST_METHOD'=>'GET'], $query);
    }

    public function testIds()
    {
        $r = $this->request([]);
        $this->assertSame($r->ids, []);
        $this->assertSame($r->method, 'GET');
        
        $r = $this->request(['id'=>'']);
        $this->assertSame($r->ids, []);

        $r = $this->request(['id'=>'0']);
        $this->assertSame($r->ids, ['0']);

        $r = $this->request(['id'=>'foo||0']);
        $this->assertSame($r->ids, ['foo','0']);
    }

    public function testPsr7() {
        if (class_exists('GuzzleHttp\Psr7\ServerRequest')) {
            $serverRequest = new \GuzzleHttp\Psr7\ServerRequest('GET','http://example.org/');

            $req = $serverRequest->withQueryParams(['id'=>'0']);

            $expect = $this->request(['id'=>'0']);
            $this->assertEquals(Request::fromPsr7($req), $expect);

            $req = new \GuzzleHttp\Psr7\ServerRequest('OPTIONS','http://example.org/');
            $expect = $this->request();
            $expect->method = 'OPTIONS';
            $this->assertEquals(Request::fromPsr7($req), $expect);
        }
    }

    public function testFormat()
    {
        $r = $this->request([]);
        $this->assertSame($r->format, 'json');

        $r = $this->request(['format'=>'xml']);
        $this->assertSame($r->format, 'xml');
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
