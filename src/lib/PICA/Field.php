<?php
declare(strict_types=1);

namespace PICA;


class Field
{
    private $tag;
    private $occ;
    private $subfields = [];

    /**
     * Parse plain PICA.
     */
    function __construct(string $field) {
        if (preg_match('!^([0-2][0-9][0-9][A-Z@])(/([0-9][0-9]))?\s(\$.+)!', $field, $match)) {
            $this->tag = $match[1];
            $this->occ = $match[3] === '' ? null : $match[3];
            $sf = preg_split('/\$([^$])/',$match[4],-1, PREG_SPLIT_DELIM_CAPTURE);
            array_shift($sf);
            $this->subfields = array_map( function($s) { return str_replace('$$','$',$s); }, $sf );
        } else {
            throw new \InvalidArgumentException("Cannot parse PICA field: $field");
        }
    }

    /**
     * Get tag, occurrence, subfields or the first or all subfield values.
     *
     *     $field->tag;         # tag                (string)
     *     $field->occ;         # occurrence         (string or null)
     *     $field->subfields;   # subfields          (array)
     *     $field->a;           # first subfield 'a' (string)
     *     $field->aa;          # all subfields 'a'  (array)
     */
    function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        } 
        if (preg_match('/^([^$])\1?$/', $name)) {
            if (strlen($name) == 1) {
                return $this->value($name[0]);
            } else {
                return $this->values($name[0]);
            }
        }
    }

    function value($name) {
        $sf = $this->subfields;
        for ($i=0; $i<count($sf); $i+=2) {
            if ($sf[$i] == $name) {
                return $sf[$i+1];
            }
        }
    }
    
    function values($name) {
        $values = [];
        $sf = $this->subfields;
        for ($i=0; $i<count($sf); $i+=2) {
            if ($sf[$i] == $name) {
                $values[] = $sf[$i+1];
            }
        }
        return $values;
    }

    /**
     * Stringify as plain PICA.
     */
    function __toString() {
        $str = $this->tag;
        if (isset($this->occ)) {
            $str .= '/' . $this->occ;
        }
        $str .= ' ';

        $sf = $this->subfields;
        for ($i=0; $i<count($sf); $i+=2) {
            $str .= '$' . $sf[$i] . str_replace('$','$$',$sf[$i+1]);
        }

        return $str;
    }
}
