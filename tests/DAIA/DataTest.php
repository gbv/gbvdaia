<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;

class DAIADataTest extends TestCase
{
    public function testEmptyConstructor()
    {
        foreach (['Entity', 'Item', 'Chronology'] as $class) {
            $class = "DAIA\\$class";
            $object = new $class();
            $this->assertSame("$object", '{}');
        }
    }

    public function testEntity()
    {
        // stringify as JSON
        $e = new Entity(['id'=>'x:y']);
        $this->assertSame("$e", '{"id":"x:y"}');

        // pretty printed JSON
        $this->assertSame($e->json(), "{\n    \"id\": \"x:y\"\n}");
    }

    public function testDocument()
    {
        $d = new Document("i:d");
        $this->assertSame("$d", '{"id":"i:d"}');

        $d->item = [];
        $this->assertSame("$d", '{"id":"i:d","item":[]}');
    }

    public function testItem()
    {
        $item = new Item();
        $this->assertSame("$item",'{}');
    }
   
    public function testChronology()
    {
        $c = new Chronology("2012");
        $this->assertSame("$c", '{"about":"2012"}');
    }
}
