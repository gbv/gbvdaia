<?php
declare(strict_types=1);

namespace GBV\DAIA;

use Psr\Log\LoggerInterface;

/** @package GBVDAIA */
class GitConfig extends FileConfig
{
	/**
	 * Create a new configuration directory with a git repository.
	 *
     * @throws ConfigException
	 */
    public function __construct(string $dir, $remote, int $update, LoggerInterface $logger=null)
    {
        $this->setLogger($logger ?? new Logger());

        if (!is_dir($dir) and !@mkdir($dir)) {
			throw new ConfigException("missing config directory $dir");
		}
		
		$this->dir = $dir;

		try {
			if (!is_dir("$dir/.git")) {
				$logger->info("setting up git repository from $remote");
                $this->git("init .");
                if ($remote) {
    			    $this->git("remote add origin $remote");
                }
            }

            $origin = $this->origin();
            if ($remote and $origin != $remote) {
                throw new ConfigException("wrong git remote $origin");
            }
            
            $branch = $this->branch();
            if ($branch != 'master') {
                throw new ConfigException("wrong git branch $branch");
            }

            # TODO: if this fails, don't die!
            if (time() - $update > $this->lastFetch()) {
                $logger->info("pull git from remote");
                $pull = $this->git("pull origin master");
                $logger->info($pull);
            }

		} catch(\RuntimeException $e) {
			throw new ConfigException("failed to initialize git repository $dir", 0, $e);
		}

		$this->readConfig();
    }

    /**
     * @throws RuntimeException
     */
    public function origin(): string
    {
        return $this->git('config --get remote.origin.url');
    }

    /**
     * @throws RuntimeException
     */
    public function branch(): string
    {
        return $this->git('symbolic-ref --short HEAD');
    }

    /**
     * Time when the repository was last fetched from remote.
     *
     * @throws RuntimeException
     */
    public function lastFetch(): int
    {        
        return @stat($this->dir."/.git/FETCH_HEAD")[9] ?? 0;
    }

    /**
     * Execute git in the repository directory.
     *
     * Code adopted from package SebastianBergmann\Git.
     *
     * @throws RuntimeException
     */
    private function git(string $command): string
    {
		$command = 'cd ' . escapeshellarg($this->dir) . '; git ' . $command . ' 2>&1';
        $command = "LC_ALL=en_US.UTF-8 $command";

        exec($command, $output, $return);

        if ($return !== 0) {
            array_unshift($output, $command);
            throw new \RuntimeException(implode("\r\n", $output));
        }

        return implode("\n", $output);
    }
}
