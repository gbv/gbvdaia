<?php declare(strict_types=1);

namespace DAIA;

class DocumentTest extends \PHPUnit\Framework\TestCase
{
    public function testDocument() {
        $doc = new Document(["id"=>"i:d"]);
        $this->assertSame("$doc", '{"id":"i:d"}');

        $doc->item = [];
        $this->assertSame("$doc", '{"id":"i:d","item":[]}');

        $doc->addItem(['id'=>'i:d']);
        $this->assertEquals($doc, new Document([
            'id' => 'i:d',
            'item' => [ new Item(['id'=>'i:d']) ]
        ]));
    }
}
