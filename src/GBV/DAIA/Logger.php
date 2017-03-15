<?php declare(strict_types=1);

namespace GBV\DAIA;

use Psr\Log\LogLevel;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Processor\PsrLogMessageProcessor;

/**
 * @package GBVDAIA 
 **/
class Logger extends \Monolog\Logger {
    private $dir;
    private $fallbackHandler;
	private $accessLogger;

    public function __construct(string $dir=null, $level=LogLevel::NOTICE)
    {
        parent::__construct('GBVDAIA');
		$this->pushProcessor(new PsrLogMessageProcessor());

        $level = strtolower($level);

        // log to STDERR by default
        $this->fallbackHandler = $this->logfile(null, $level);
        $this->pushHandler($this->fallbackHandler);

        if ($dir) {
            $dir = $dir[0]=='/' ? $dir : getcwd()."/$dir";
            if (is_dir($dir) and is_writeable($dir)) {
                $this->dir = $dir;
                $this->popHandler();

                $handler = $this->logfile('error.log', LogLevel::ERROR);
                $this->pushHandler($handler);

                $handler = $this->logfile('default.log', $level);
                $this->pushHandler($handler);

				$this->accessLogger = new \Monolog\Logger('GBVDAIA',
					[$this->logfile('access.log', LogLevel::NOTICE, "%datetime% %message%\n")],
					[new PsrLogMessageProcessor()]
				);

                // TODO: add line number and stack trace
                $handler = $this->logfile('crash.log', LogLevel::DEBUG);
                $handler = new FingersCrossedHandler($handler, \Monolog\Logger::CRITICAL);
                $this->pushHandler($handler);
            } else {
                $this->error("Log dir is not writable: $dir");
            }
        }
    }

	public function access($ip, $path, $request) {
		try {
			$this->accessLogger->notice(
				"{ip} {path} {request}",
				[ 'request' => $request, 'path' => $path, 'ip' => $ip ]
			);
		} catch(\Throwable $e) {
		}
	}

    /**
     * Create a new StreamHandler writing to logfile or STDERR.
     */
    protected function logfile($stream, $level, $format=null) {
        if ($stream) {
            $stream = $this->dir . "/$stream";
        } else {
            $stream = 'php://stderr';
        }
        $handler = new StreamHandler($stream, $level);

        $output = "[%datetime%] %level_name% %message%\n";
        $formatter = new LineFormatter($format ?? $output);
        $handler->setFormatter($formatter);

        return $handler;
    }

    /**
     * Switch to fallback handler if logging failed, e.g. logfile not writeable.
     */
    public function addRecord($level, $message, array $context = []) {
        try {
            return parent::addRecord($level, $message, $context);
        } catch(\Throwable $e) {
            if( $this->handlers[0] != $this->fallbackHandler ) {
                $this->handlers = [$this->fallbackHandler];
            }
            $this->critical('Logging failed',['exception'=>$e]);
            return parent::addRecord($level, $message, $context);
        }
    }
}
