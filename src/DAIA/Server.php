<?php
declare(strict_types=1);

namespace DAIA;

/**
 * DAIA Server boilerplate.
 * 
 * A Server derived from this class must implement method `queryHandler`.
 * This method should return a DAIA Response or throw a DAIA Error. A Server
 * can be queried via method `query`.
 *
 * ~~~php
 * use DAIA\Request;
 * use DAIA\Response;
 *
 * class MyDAIAServer extends \DAIA\Server {
 *    public function queryHandler(Request $request): Response {
 *        if (...) throw new \DAIA\Error(...);
 *        return new Response(...);
 *    }
 * }
 *
 * $server = new MyDAIAServer();
 * $response = $server->query($request);
 * ~~~
 *
 * @package DAIA
 */
abstract class Server
{
    public abstract function queryHandler(Request $request): Response;

    protected function exceptionHandler($context) { }

    public function query(Request $request): ResponseData
    {
        if ($request->method == 'OPTIONS') {
            return new OptionsResponse(); 
        }

        try {
            if (array_search($request->method, ['GET','HEAD','OPTIONS']) === false) {
                throw new Error(405, 'Unexpected HTTP verb');
            }
            return $this->queryHandler($request);
        } catch(Error $e) {
            return $e->response;
        } catch(\Throwable $e) {
            if ($this->exceptionHandler([
                'request' => $request,
                'server' => $this, 
                'exception' => $e
            ])) {
                throw $e;
            }
            return new ErrorResponse(500, 'Unexpected internal server error');
        }
    }
}
