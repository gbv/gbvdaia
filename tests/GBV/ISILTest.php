<?php

namespace GBV;

use PHPUnit\Framework\TestCase;


class ISILTest extends TestCase {

    public function testISIL() {
        $this->assertFalse(ISIL::ok('DE-'));
        $this->assertTrue(ISIL::ok('DE-1'));
    }
}
