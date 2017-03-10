<?php
declare(strict_types=1);

namespace GBV\DAIA;

use DAIA\Entity;
use Psr\Log\LogLevel;
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
    protected $dir;

    private $log;
    private $indicators;

    public function __construct(string $dir, $level = LogLevel::NOTICE)
    {
        $this->dir = $dir;
        $this->logger($level); // make sure to have a logger
        $this->readConfig();
    }

    protected function readConfig()
    {
        // TODO: read and enable logging configuration
    }

    static function defaultLogger($level = LogLevel::NOTICE): LoggerInterface
    {
        // log warnings and errors to STDERR by default
        $log = new \Monolog\Logger('GBVDAIA');
        $stream = new \Monolog\Handler\StreamHandler('php://stderr', $level);
        $stream->getFormatter()->ignoreEmptyContextAndExtra(true);
        $log->pushHandler($stream);
        return $log;
    }

    public function logger(): LoggerInterface
    {
        if (!$this->log) {
            $this->log = $this->defaultLogger();
        }
        return $this->log;
    }


    private function readFile($file): string
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
            $this->indicators = Yaml::parse($this->readFile($file));
            $this->log->debug("Read $file");
            # TODO: validate file
        } catch (FileException $e) {
            $this->log->error($e->getMessage());
        } catch (ParseException $e) {
            $this->log->error("Failed to parse $file: ".$e->getMessage());
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

        $this->log->debug("loanIndicator", [ 
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
}
