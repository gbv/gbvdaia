<?php declare(strict_types=1);

namespace DAIA;

/**
 * Information about an unavailable service.
 *
 * See <https://purl.org/NET/DAIA#unavailable>
 * @package DAIA
 */
class Unavailable extends ServiceStatus
{
    protected $expected;
    protected $queue;
    
    protected function setExpected($value) {
        if ($value == 'unknown' or Data::isDatetime($value)) {
            $this->expected = $value;
        }
    }

    protected function setQueue($value) {
        $this->queue = $value >= 0 ? (int)$value : 0;
    }
}
