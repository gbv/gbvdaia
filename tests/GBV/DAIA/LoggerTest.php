<?php

namespace GBV\DAIA;

class LoggerTest extends \PHPUnit\Framework\TestCase
{
    public function testLogger() {
        $logger = new Logger();
        $this->assertTrue(!!$logger);
    }
}
