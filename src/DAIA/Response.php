<?php
declare(strict_types=1);

namespace DAIA;

class Response extends ResponseData
{
    public $document = [];
    public $institution;
    public $timestamp;

    public function __construct()
    {
        $this->timestamp = date("c", time());
    }

    public function getStatusCode(): int
    {
        return 200;
    }
}
