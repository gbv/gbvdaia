<?php

use PHPUnit\Framework\TestCase;

use DAIA\Entity;
use DAIA\Response;
use DAIA\Document;
use DAIA\Item;
use DAIA\Chronology;
use DAIA\Available;
use DAIA\Unavailable;
use DAIA\Error;


class DAIADataTest extends TestCase {

    public function testEmpty() {
        foreach (['Entity', 'Item', 'Chronology'] as $class) {
            $class = "DAIA\\$class";
            $object = new $class();
            $this->assertSame("$object",'{}');
        }
    }

    public function testEntity() {

        # stringify as JSON
        $e = new Entity();
        $e->id = "x:y";
        $this->assertSame("$e",'{"id":"x:y"}');

        # pretty printed JSON
        $this->assertSame($e->json(),"{\n    \"id\": \"x:y\"\n}");
        
    }

    public function testResponse() {
        # TODO        
    }

    public function testDocument() {
        $d = new Document("i:d");
        $this->assertSame("$d",'{"id":"i:d"}');

        $d->item = [];
        $this->assertSame("$d",'{"id":"i:d","item":[]}');
    }

    public function testItem() {
        # TODO        
    }

   
    public function testChronology() {
        $c = new Chronology("2012");
        $this->assertSame("$c",'{"about":"2012"}');
    }

    public function testAvailable() {
        # TODO        
    }
 
    public function testUnavailable() {
        # TODO        
    }

    public function testError() {
        $e = new Error(400, "invalid_request");
        $this->assertSame("$e",'{"code":400,"error":"invalid_request"}');
    }
}
