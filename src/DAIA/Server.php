<?php declare(strict_types=1);

namespace DAIA;

/**
 * DAIA Server boilerplate.
 * 
 * A Server derived from this class must implement method `queryHandler`.
 * This method should return a Response or throw a DAIA Error. A Server
 * can be queried via method `query`.
 *
 * ~~~php
 * use DAIA\Request;
 * use DAIA\Response
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

    protected function exceptionHandler(Request $request, \Throwable $exception)
    {
       // overridate in subclass and return true to rethrow exception
    }

    public function query(Request $request): HTTPResponse
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
            $rethrow = False;
            try {
                $rethrow = $this->exceptionHandler($request, $e);
            } catch (\Throwable $e) {
                // ignore broken exception handlers
            }
            if ($rethrow) {
                throw $e;
            } else {
                return new ErrorResponse(500, 'Unexpected internal server error');
            }
        }
    }
}
