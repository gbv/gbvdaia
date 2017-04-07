<?php declare(strict_types=1);

namespace GBV\DAIA;

use DAIA\Entity;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

/** @package GBVDAIA */
class FileException extends \RuntimeException
{
}

/** @package GBVDAIA */
class FileConfig implements Config
{
    use \Psr\Log\LoggerAwareTrait;

    protected $dir;
    protected $log;

    private $indicators;

    public function __construct(string $dir, LoggerInterface $logger=null)
    {
        $this->setLogger($logger ?? new Logger());
        $this->dir = $dir;
        $this->readConfig();
    }

    protected function readConfig()
    {
        $this->logger->warn("readConfig not implemented yet!");
        // TODO: read and enable logging configuration
    }

    protected static function readFile($file): string
    {
        $content = @file_get_contents($file);
        if ($content === false) {
            throw new FileException("Failed to read file $file");
        }
        return $content ;
    }

    private function readLoanIndicators()
    {
        $this->indicators = [];
        $file = $this->dir . "/ausleihindikator.yaml";
        try {
            $this->indicators = Yaml::parse(static::readFile($file));
            $this->logger->debug("Read $file");
            # TODO: validate file
        } catch (FileException $e) {
            $this->logger->error($e->getMessage());
        } catch (ParseException $e) {
            $this->logger->error("Failed to parse $file: ".$e->getMessage());
        }
    }

    public function loanIndicator(string $dbkey, string $indicator): array
    {
        if (!$this->indicators) {
            $this->readLoanIndicators();
        }

        $standard = $this->indicators[''] ?? [];
        $dbconfig = $this->indicators[$dbkey] ?? $standard;
        if ($indicator == '') {
            $indicator = $dbconfig['default'] ?? $standard['default'] ?? '';
        }

        $services = $dbconfig[$indicator] ?? $standard[$indicator] ?? [];
		krsort($services);

        $this->logger->debug("loanIndicator", [ 
            'dbkey'     => $dbkey, 
            'indicator' => $indicator,
            'result'    => $services
        ]);

        return $services;
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

    public static $defaultLoanIndicator = [

    ];

    public function proxyUrl(string $isil): string {
        if ($isil == 'Hil2') {
            return 'http://lhhil.gbv.de:7242/DE-Hil2/daia';
        }

        return '';
    }
}
