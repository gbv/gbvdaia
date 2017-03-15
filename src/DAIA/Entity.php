<?php declare(strict_types=1);

namespace DAIA;

/**
 * Information about an entity (institution, department, storage, limitation).
 *
 * See <https://purl.org/NET/DAIA#simple-data-types>
 * @package DAIA
 */
abstract class Entity extends Data
{
    protected $id;
    protected $href;
    protected $content;
}
