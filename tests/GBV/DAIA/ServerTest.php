<?php

namespace GBV\DAIA;

use PHPUnit\Framework\TestCase;
use GBV\DocumentID;
use DAIA\Item; 
use DAIA\Entity; 
use DAIA\Available; 
use DAIA\Unavailable; 
use Psr\Log\LogLevel;

define('EXAMPLES', dirname(__FILE__)."/examples");


class ServerTest extends TestCase {

    public function testConstructor() {
        $config = new FileConfig('-', LogLevel::CRITICAL );
        $service = new Server($config);
        $this->assertTrue(!!$service);
    }

    public function testConvertHolding() {
        $config = new FileConfig( EXAMPLES, LogLevel::ERROR );
        $service = new Server($config);

        $holding = new Holding();
    	$holding->epn = '98765';
    	$holding->label = 'foo/bar';
    	$holding->indikator = 'f';  # Lesesaalausleihe / nur Kopie in die Fernleihe
		$holding->status = '0';		# verfügbar

		# TODO: href, queue, date, sst etc.
      
        $id = DocumentID::parse('opac-de-12345:ppn:1234567');
        $item = $service->convertHolding($id, $holding);

        $expect = new Item([
            'id' => 'http://uri.gbv.de/document/opac-de-12345:epn:98765',
            'label' => 'foo/bar',
			'unavailable' => [
				new Unavailable([ 'service' => 'loan' ]),
			],
			'available' => [	
				new Available([ 'service' => 'presentation' ]),
				new Available([
					'service' => 'interloan',
					'limitation' => [
						new Entity(['content' => 'nur Kopie'])
					]
				]),
			]
        ]);
        $this->assertEquals($item, $expect);
    }
}
