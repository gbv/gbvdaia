<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    /**
     * @expectedException DAIA\Error
     */
    public function testError() 
    {
        throw new Error(500);
    }
}
