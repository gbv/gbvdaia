<?php
declare(strict_types=1);

namespace DAIA;

/**
 * DAIA Server boilerplate.
 * 
 * A Server derived from this class must implement method `queryImplementation`.
 * This method should return a DAIA Response or throw a DAIA Error. A Server
 * can be queried via method `query` that always returns a DAIA ResponseData
 * (Response or ErrorResponse).
 *
 * ~~~php
 * use DAIA\Request;
 * use DAIA\Response;
 *
 * class MyDAIAServer extends \DAIA\Server {
 *    public function queryImplementation(Request $request): Response {
 *        if (...) throw new \DAIA\Error(...);
 *        return new Response(...);
 *    }
 * }
 *
 * $server = new MyDAIAServer();
 * $response = $server->query($request);    # \DAIA\Response or \DAIA\ErrorResponse
 * ~~~
 *
 * @package DAIA
 */
abstract class Server
{
    public function query(Request $request): ResponseData
    {
        if ($request->method == 'OPTIONS') {
            return new OptionsResponse(); 
        }

        try {
            if (array_search($request->method, ['GET','HEAD','OPTIONS']) === false) {
                throw new Error(405, 'Unexpected HTTP verb');
            }
            return $this->queryImplementation($request);
        } catch(Error $e) {
            return $e->response;
        } 
        # TODO: catch other kinds of errors?
    }

    abstract public function queryImplementation(Request $request): Response;
}
