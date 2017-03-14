<?php

namespace GBV\DAIA;

use PHPUnit\Framework\TestCase;


class ConfigTest extends TestCase {
    public function testFileConfig() {
        $logger = new Logger();
        $conf = new FileConfig("test/config", $logger);
        $this->assertTrue(!!$conf);
    }
}
