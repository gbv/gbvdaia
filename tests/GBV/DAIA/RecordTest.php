<?php

namespace GBV\DAIA;

use PHPUnit\Framework\TestCase;


class RecordTest extends TestCase {

    public function testConstructor() {
        $pica = file_get_contents(dirname(__FILE__)."/examples/example.pica");

        $rec = new Record($pica);
        $this->assertSame($rec->mak, 'Aau');
        $this->assertSame(count($rec->holdings), 1);
        $this->assertSame(count($rec->holdings['90']), 1);

        $holding = new Holding();
    	$holding->epn = '134005945';
    	$holding->label = 'ERZ 608:A24 : I-02';
    	$holding->indikator = 'f';
    	$holding->status = '0';
        $this->assertEquals($holding, $rec->holdings['90'][0]);
    }    
}
