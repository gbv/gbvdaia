<?php declare(strict_types=1);

namespace DAIA;

/**
 * Information about an item.
 *
 * See <https://purl.org/NET/DAIA#items>
 * @package DAIA
 */
class Item extends Data
{
    protected $id;
    protected $href;
    protected $part;
    protected $label;
    protected $chronology;
    protected $department;
    protected $storage;
    protected $available;
    protected $unavailable;

    protected function fieldRepeatable($field): bool {
        return $field == 'available' or $field == 'unavailable';
    }
}
