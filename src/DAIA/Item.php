<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Information about an item.
 *
 * See <https://purl.org/NET/DAIA#items>
 * @package DAIA
 */
class Item extends Data
{
    public $id;
    public $href;
    public $part;
    public $label;
    public $chronology;
    public $department;
    public $storage;
    public $available;
    public $unavailable;
}
