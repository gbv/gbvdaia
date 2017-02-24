<?php

namespace GBV\DAIA;

use PHPUnit\Framework\TestCase;


class RecordTest extends TestCase {

    public function testConstructor() {
        $pica = <<<'PICA'
002@ $0Aau
003@ $016523315X
101@ $a90
101B $028-07-97$t10:40:30.861
101D $028-07-97$b5416$a0000
101U $0utf8
145Z/01 $aBWL 090
201@/01 $anur vor Ort benutzbar|Nicht ausleihbar$b0$e134005945$mmon$f1$unur vor Ort benutzbar$vNicht ausleihbar
201B/01 $027-02-14$t15:37:38.000
201D/01 $027-02-14$b5406$a3090
201F/01 $00
201U/01 $0utf8
203@/01 $0134005945
209A/01 $aERZ 608:A24 : I-02$df$x00
209C/01 $a93:09096$x00
PICA;

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
