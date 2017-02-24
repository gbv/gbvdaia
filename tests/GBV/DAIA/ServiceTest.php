<?php

namespace GBV\DAIA;

use PHPUnit\Framework\TestCase;


class ServiceTest extends TestCase {
    public function testConstructor() {
        $config = new FileConfig("test/config");
        $service = new Service($config);
        $this->assertTrue(!!$service);
    }
}
