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

        foreach ([[],123,'P0001-00-04T00:00:00'] as $value) {
            $s->delay = $value;
            $this->assertSame($s->delay, null);
        }

        foreach (['-P1D','P6YT5M','unknown', null] as $value) {
            $s->delay = $value;
            $this->assertSame($s->delay, $value);
        }
    }
 
    public function testUnavailable()
    {
        $s = new Unavailable(['service'=>'remote', 'queue'=>3]);
        $this->assertSame($s->service, 'http://purl.org/ontology/dso#Remote');
        $this->assertSame($s->queue, 3);        
    }
}
