<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Information about an available service.
 *
 * See <https://purl.org/NET/DAIA#available>
 * @package DAIA
 */
class Available extends ServiceStatus
{
    public $delay;
}
