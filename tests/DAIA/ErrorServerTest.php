<?php
namespace DAIA;

use PHPUnit\Framework\TestCase;

class ErrorServerTest extends TestCase
{
    private $server;

    public function setUp()
    {        
        $this->server = new ErrorServer(new Error(404,'not found'));
    }

    public function testQueryResponse() 
    {        
        $response = $this->server->queryResponse(new Request());
        $this->assertEquals($response, $this->server->error->response);
    }

    /**
     * @expectedException DAIA\Error
     */
    public function testQuery() 
    {        
        $this->server->query(new Request());
    }
}
