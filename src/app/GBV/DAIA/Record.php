<?php
declare(strict_types=1);

namespace GBV\DAIA;

use PICA\Field;


/**
 * PICA+ record stripped down to information required for DATA.
 */
class Record
{
    public $mak;
    public $holdings = [];

    /**
     * Parse plain PICA+.
     */
    function __construct(string $pica) {
        $fields = explode("\n", $pica);
        $fields = array_values(preg_grep('/^(002@|101@|201@|209A)/', $fields));

        $holdings = [];
        foreach ($fields as $field) {
            $field = new Field($field);

            if ($field->tag == '002@') {
                $this->mak = $field->value('0');
            } elseif($field->tag == '101@') {
                $iln = $field->a;
                if (!isset($this->holdings[$iln])) {
                    $this->holdings[$iln] = [];
                }
                $holdings = &$this->holdings[$iln];
            } else { # '201@' or '209A'
                $occ = (int)$field->occ - 1;
                if (!isset($holdings[$occ])) {
                    $holdings[$occ] = new Holding();
                };
                $holding = &$holdings[$occ];

                if($field->tag == '201@') {
                    $holding->epn    = $field->e;       # 201@ $e : EPN
                    $holding->href   = $field->l;       # 201@ $l : Link auf das Ausleihsystem
                    $holding->status = $field->b;       # 201@ $b : aktueller Ausleihstatus 
                    # (0:verfügbar, 1:bestellbar, 6: unbekannt (Bandliste), sonst: nicht verfügbar)
                    $holding->queue  = $field->n;       # 201@ $n : Anzahl Vormerkungen
                } elseif($field->tag == '209A') {
                    $holding->label     = $field->a;    # 209A $a : Signatur
                    $holding->indikator = $field->d;    # 209A $d : Ausleihindikator
                    $holding->sst       = $field->f;    # 209A $f : Sonderstandort
                }
            }
        }
    }
}

class Holding
{
    public $epn;
    public $label;                        
    public $sst;
    public $indikator;
    public $href;
    public $status;
    public $queue;
}
