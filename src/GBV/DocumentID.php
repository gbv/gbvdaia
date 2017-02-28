<?php

namespace GBV;


class DocumentID 
{
    public $requested;
    public $dbkey;
    public $ppn;

    static function parse($requested, $isil=NULL) {
        if (preg_match(
                '!(((http://uri.gbv.de/document/)?([a-z0-9-]+)):)?ppn:([0-9][0-9X]+)!',
                $requested, $match)) {
            $id = new DocumentID();
            $id->requested = $requested;
            $id->dbkey = $match[4];            
            $id->ppn   = $match[5];
            if (!$id->dbkey) {
                if (ISIL::ok($isil)) {
                    $id->dbkey = 'opac-' . strtolower($isil);
                } else {
                    return;
                }
            } 
            return $id;
        }
    }

    function short() {
        return $this->dbkey . ':ppn:' . $this->ppn;
    }

    function uri() {
        return 'http://uri.gbv.de/document/' . $this->short();
    }
}
