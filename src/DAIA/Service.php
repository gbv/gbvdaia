<?php
declare(strict_types=1);

namespace DAIA;

class Service
{
    public $service;
    public $href;
    public $limitation;

    public function __construct(string $service)
    {
        if (preg_match('/(presentation|loan|remote|interloan|openaccess)$/', $service)) {
            $service = 'http://purl.org/ontology/dso#'.ucfirst($service);
        }
        $this->service = $service;
    }
}
