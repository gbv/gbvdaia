<?php
declare(strict_types=1);

namespace DAIA;

class Available extends Data
{
    public $service;
    public $href;
    public $delay;
    public $limitation;

    function __construct(string $service) {
        $this->service = $service;
    }
}
