<?php

namespace GBV\DAIA;

use PHPUnit\Framework\TestCase;


class ConfigTest extends TestCase {
    public function testFileConfig() {
        $conf = new FileConfig("test/config");
        $this->assertTrue(!!$conf);
    }
}
