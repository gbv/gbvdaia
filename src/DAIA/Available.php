<?php declare(strict_types=1);

namespace DAIA;

/**
 * Information about an available service.
 *
 * See <https://purl.org/NET/DAIA#available>
 * @package DAIA
 */
class Available extends ServiceStatus
{
    protected $delay;

    protected function setDelay($value) {
        try {
            $value = (string)$value;
            if ($value !== 'unknown') {
                // XML Schema datatype xsd:duration
                $interval = $value[0]=='-' ? substr($value,1) : $value;
                if (!new \DateInterval($interval) or strpos($value,':')) {
                    return;
                }
            }
            $this->delay = $value;
        } catch(\Exception $e) { }
    }
}
