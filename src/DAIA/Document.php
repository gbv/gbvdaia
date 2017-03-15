<?php declare(strict_types=1);

namespace DAIA;

/**
 * Information about a document.
 *
 * See <https://purl.org/NET/DAIA#documents>
 * @package DAIA
 */
class Document extends Data
{
    protected $id;
    protected $requested;
    protected $href;
    protected $about;
    protected $item;

    protected function fieldRequired($field): bool {
        return $field == 'id';
    }

    protected function fieldRepeatable($field): bool {
        return $field == 'item';
    }
}
