<?php declare(strict_types=1);

namespace DAIA;

class ChronologyTest extends \PHPUnit\Framework\TestCase
{
    public function testChronology()
    {
        $c = new Chronology(['about'=>2012, 'whatever'=>[1]]);
        $this->assertSame("$c", '{"about":"2012"}');
    }
}
