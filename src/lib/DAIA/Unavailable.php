<?php
declare(strict_types=1);

namespace DAIA;

class Unavailable extends Data
{
    public $service;
    public $href;
    public $expected;
    public $queue;
    public $limitation;

    function __construct(string $service) {
        $this->service = $service;
    }
}
