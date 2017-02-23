<?php
declare(strict_types=1);

namespace DAIA;

class Chronology extends Data
{
    public $about;

    function __construct(string $about = null)
    {
        $this->about = $about;
    }
}
