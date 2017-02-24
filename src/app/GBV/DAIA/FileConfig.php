<?php
declare(strict_types=1);

namespace GBV\DAIA;

use DAIA\Entity;


class FileConfig implements Config
{
    public function __construct(string $dir) {
        // TODO
    }

    public function ausleihindikator(string $dbkey, string $indikator): array {
        // start with a dummy
        return [
            'presentation' => [ 'is' => 'unavailable' ],
            'loan' => [ 'is' => 'unavailable' ],
            'interloan' => [ 'is' => 'unavailable' ]
        ];
    }

    public function sst(string $isil, string $sst): array {
        // start with a dummy
        $uri = "http://uri.gbv.de/organization/isil/$isil";
        return [
            'department' => new Entity(['uri'=>$uri]),
            'storage' => null
        ];
    }    
}
