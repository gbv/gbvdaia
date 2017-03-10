<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Information about an unavailable service.
 *
 * See <https://purl.org/NET/DAIA#unavailable>
 * @package DAIA
 */
class Unavailable extends ServiceStatus
{
    public $expected;
    public $queue;
}
