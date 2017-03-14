<?php declare(strict_types=1);

namespace GBV\DAIA;

use Psr\Log\LogLevel;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FingersCrossedHandler;

/**
 * @package GBVDAIA 
 **/
class Logger extends \Monolog\Logger {
    private $directory;

    public function __construct(string $directory=null, $level=LogLevel::WARNING)
    {
        parent::__construct('GBVDAIA');

        // log to STDERR by default
        $this->pushHandler($this->logfile(null, $level));
       
        if ($directory) {
            if (is_dir("$directory/") and is_writeable("$directory/")) {
                $this->error("Log directory is not writable: $directory");
            } else {
                $this->popHandler();

                $handler = $this->logfile('error.log', LogLevel::ERROR);
                $this->pushHandler($handler);

                $handler = $this->logfile('debug.log', LogLevel::DEBUG);
                $this->pushHandler($handler);

                // dump crash reports
                $file = "$directory/crash.log";
                $debugHandler = $this->logfile('crash.log', LogLevel::DEBUG);
                $handler = new FingersCrossedHandler($debugHandler, \Monolog\Logger::CRITICAL);
                $this->pushHandler($handler);
            }
        }
    }

    protected function logfile($file, $level) {
        $file = $file ? $this->directory . "/$file" : "php://stderr"; 
        $stream = new StreamHandler($file, $level);

        $output = "[%datetime%] %level_name%: %message% %context%\n";
        $formatter = new LineFormatter($output);
        $stream->setFormatter($formatter);

        return $stream;
    }
}
