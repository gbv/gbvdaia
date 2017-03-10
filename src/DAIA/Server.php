<?php
declare(strict_types=1);

namespace DAIA;

/**
 * DAIA Server boilerplate.
 * 
 * @package DAIA
 */
abstract class Server
{
    public function queryResponse(Request $request): ResponseData
    {
        if ($request->method == 'OPTIONS') {
            return new OptionsResponse(); 
        }

        if (array_search($request->method, ['GET','HEAD','OPTIONS']) === false) {
            return new Error(405, 'Unexpected HTTP verb');
        }

        try {
            return $this->query($request);
        } catch(Error $e) {
            return $e->response;
        } 
        # TODO: catch other kinds of errors?
    }

    abstract public function query(Request $request): Response;
}
