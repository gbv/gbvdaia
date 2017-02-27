<?php

use PHPUnit\Framework\TestCase;
use DAIA\Request;


class DAIARequestTest extends TestCase
{

    public function testIds()
    {
        $r = new Request();
        $this->assertSame($r->ids,[]);
        $this->assertSame($r->method, 'GET');
        
        $r = new Request(['id'=>'']);
        $this->assertSame($r->ids,[]);

        $r = new Request(['id'=>'foo|bar']);
        $this->assertSame($r->ids,['foo','bar']);

        $r = new Request([],[],'OPTIONS');
        $this->assertSame($r->method, 'OPTIONS');
    }
}
