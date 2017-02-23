<?php

use PHPUnit\Framework\TestCase;


class DocumentIDTest extends TestCase {

    public function testID() {
        foreach (['x', 'ppn:123'] as $id) {
            $this->assertNull(DocumentID::parse($id));
        }

        $id = DocumentID::parse('ppn:123','DE-7');
        $this->assertSame($id->short(), 'opac-de-7:ppn:123');
        $this->assertSame($id->uri(), 'http://uri.gbv.de/document/opac-de-7:ppn:123');

    }
}
