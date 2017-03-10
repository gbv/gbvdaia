<?php
declare(strict_types=1);

namespace DAIA;

/**
 * Information about an available or unavailable service.
 *
 * See <https://purl.org/NET/DAIA#services>
 * @package DAIA
 */
abstract class ServiceStatus extends Data
{
    public $service;
    public $href;
    public $limitation;

    public function __construct(array $data)
    {
        parent::__construct($data);        
        if (preg_match('/(presentation|loan|remote|interloan|openaccess)$/', $this->service)) {
            $this->service = 'http://purl.org/ontology/dso#'.ucfirst($this->service);
        }
    }
}
