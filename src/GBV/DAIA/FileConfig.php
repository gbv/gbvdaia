<?php
declare(strict_types=1);

namespace GBV\DAIA;

use DAIA\Entity;
use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;

class FileConfig implements Config
{
    public $loggerInterface;


    public function __construct(string $dir, $logLevel = LogLevel::NOTICE)
    {

        // log warnings and errors to STDERR by default
        $this->loggerInterface = new \Monolog\Logger('GBVDAIA');
        $stream = new \Monolog\Handler\StreamHandler('php://stderr', $logLevel);
        $this->loggerInterface->pushHandler($stream);

        // TODO: read and apply configuration from $dir
    }

    public function logger(): LoggerInterface
    {
        return $this->loggerInterface;
    }

    public function ausleihindikator(string $dbkey, string $indikator): array
    {
        // start with a dummy
        return [
            'presentation' => [ 'is' => 'unavailable' ],
            'loan' => [ 'is' => 'unavailable' ],
            'interloan' => [ 'is' => 'unavailable' ]
        ];
    }

    public function sst(string $isil, string $sst): array
    {
        // start with a dummy
        $uri = "http://uri.gbv.de/organization/isil/$isil";
        return [
            'department' => new Entity(['uri'=>$uri]),
            'storage' => null
        ];
    }
}
