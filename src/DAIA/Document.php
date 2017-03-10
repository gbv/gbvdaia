<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Information about a document.
 *
 * See <https://purl.org/NET/DAIA#documents>
 * @package DAIA
 */
class Document extends Data
{
    public $id;
    public $requested;
    public $href;
    public $about;
    public $item;

    public function __construct(string $id, string $requested = null)
    {
        $this->id = $id;
        $this->requested = $requested;
    }
}
