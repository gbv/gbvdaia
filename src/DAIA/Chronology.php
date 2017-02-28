<?php
declare(strict_types=1);

namespace DAIA;

class Chronology extends Data
{
    public $about;

    public function __construct(string $about = null)
    {
        $this->about = $about;
    }
}
