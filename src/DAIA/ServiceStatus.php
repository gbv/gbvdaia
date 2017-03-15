<?php declare(strict_types=1);

namespace DAIA;

/**
 * Information about an available or unavailable service.
 *
 * See <https://purl.org/NET/DAIA#services>
 * @package DAIA
 */
abstract class ServiceStatus extends Data
{
    protected $service;
    protected $href;
    protected $limitation;

    protected function setService($value) {
        if (preg_match('/(presentation|loan|remote|interloan|openaccess)$/', $value)) {
            $this->service = 'http://purl.org/ontology/dso#'.ucfirst($value);
        }
    }

    protected function fieldRequired($field): bool {
        return $field == 'service';
    }

    protected function fieldRepeatable($field): bool {
        return $field == 'limitation';
    }
}
