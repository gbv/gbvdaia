<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Information an entity (institution, department, storage, limitation).
 *
 * See <https://purl.org/NET/DAIA#simple-data-types>
 * @package DAIA
 */
class Entity extends Data
{
    public $id;
    public $href;
    public $content;
}
