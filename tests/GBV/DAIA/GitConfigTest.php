<?php

namespace GBV\DAIA;

use PHPUnit\Framework\TestCase;

class GitConfigTest extends TestCase {
    public $tempDirs = [];

    public function tempDir()
    {
        $name = bin2hex(random_bytes(8));
        $dir = sys_get_temp_dir()."/$name";
        if (@mkdir($dir) === false) {
            throw new \RuntimeException("failed to create $dir");
        }
        $this->tempDirs[] = $dir;
        return $dir;
    }

    public function setUp() {
        register_shutdown_function(function() {
            foreach ($this->tempDirs as $dir) {
        		exec('rm -rf ' . escapeshellarg($dir));
            }
        });
    }

    public function testGitConfig() {
        $dir = $this->tempDir();
        $later = time() + 9999;
        $conf = new GitConfig($dir, $later, "http://example.org/");
        $this->assertSame($conf->origin(), "http://example.org/");
        $this->assertSame($conf->lastFetch(), 0);       

        $conf = new GitConfig($dir, $later);
        $this->assertSame($conf->origin(), "http://example.org/");
    }
}
