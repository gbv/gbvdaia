<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Chronology information of an Item.
 *
 * See <https://purl.org/NET/DAIA#chronology>
 * @package DAIA
 */
class Chronology extends Data
{
    public $about;

    public function __construct(string $about = null)
    {
        $this->about = $about;
    }
}
