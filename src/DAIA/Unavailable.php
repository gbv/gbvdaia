<?php
declare(strict_types=1);

namespace DAIA;

class Unavailable extends ServiceStatus
{
    public $expected;
    public $queue;
}
