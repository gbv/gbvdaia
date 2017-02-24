<?php
declare(strict_types=1);

namespace GBV\DAIA;

use DAIA\Response;
use DAIA\Document;
use DAIA\Item;

use GBV\DocumentID;
use GBV\ISIL;
use GBV\DAIAPICA;


class Service
{
    protected $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    static function isilFromPath(string $path)
    {
        if (preg_match('!^/isil/(.+)!', $path, $match) and ISIL::ok($match[1])) {
            return $match[1];
        }
    }


    function query($request, $isil=NULL)
    {
        $response = new Response();

        # TODO: add organization from $isil
    
        if (!count($request->ids)) {
            return $response;
        }

        # TODO: handle multiple request IDs

        $id = DocumentID::parse($request->ids[0], $isil);
        if ($id) $doc = $this->queryDocument($id);
        if ($doc) $response->document[] = $doc;
        
        return $response;
    }

    function queryDocument(DocumentID $id)
    {
        // fetch PICA record
        $url = 'http://unapi.gbv.de/?' 
             .  http_build_query(['id' => $id->short(), 'format' => 'pp']);
        error_log($url);    # TODO: proper logging

        $pica = @file_get_contents($url);
        if (!$pica) return;

        // document found
        $doc = new Document($id->uri(), $id->requested);
        # TODO: add href based on opac URL
        # TODO: add department
        
        $pica = new DAIAPICA($pica); # TODO: catch parsing error?
		# TODO: make sure that all holdings have epn not null

        foreach ($pica->holdings as $iln => $level2) {
        	foreach ($level2 as $holding) {
				$doc->item[] = $this->convertHolding($id, $holding);
			}
        }

        return $doc;
    }

    function convertHolding($id, $holding)
    {
		$item = new Item();
		$item->id = "http://uri.gbv.de/document/{$id->dbkey}:epn:{$holding->epn}";		
		if ($holding->label) {
			$item->label = $holding->label;
		}
		if ($holding->queue) {
			$item->queue = $holding->queue;
		}
		# TODO: status, sst, indikator, href
		return $item;
   	}
}
