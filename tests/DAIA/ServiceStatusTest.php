<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;

class ServiceStatusTest extends TestCase
{
    public function testAvailable()
    {
        $s = new Available(['service'=>'loan', 'href' => 'http://example.org/']);
        $this->assertSame($s->service, 'http://purl.org/ontology/dso#Loan');
        $this->assertSame($s->href, 'http://example.org/');
    }
 
    public function testUnavailable()
    {
        $s = new Unavailable(['service'=>'remote', 'queue' => 3]);
        $this->assertSame($s->service, 'http://purl.org/ontology/dso#Remote');
        $this->assertSame($s->queue, 3);
    }
}
