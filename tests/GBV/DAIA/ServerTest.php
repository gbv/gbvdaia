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
        $logger = new Logger();
        $config = new FileConfig('-', $logger);
        $client = ServerFactory::makeClient($logger);
        $service = new Server($config, $logger, $client);
        $this->assertTrue(!!$service);
    }

    public function testConvertHolding() {
        $logger = new Logger();
        $config = new FileConfig(EXAMPLES, $logger);
        $client = ServerFactory::makeClient($logger);
        $service = new Server($config, $logger, $client);

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
				[ 'service' => 'loan' ],
			],
			'available' => [	
				[ 'service' => 'presentation' ],
				[
					'service' => 'interloan',
					'limitation' => [
						['content' => 'nur Kopie']
					]
				],
			]
        ]);
        $this->assertEquals($item, $expect);
    }
}
