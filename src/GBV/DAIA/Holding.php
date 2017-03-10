<?php
declare(strict_types=1);

namespace GBV\DAIA;

use PICA\Field;

/**
 * PICA+ holding record stripped down to information required for DAIA.
 *
 * @package GBVDAIA
 */
class Holding
{
    public $epn;        # 201@ $e : EPN
    public $href;       # 201@ $l : Link auf das Ausleihsystem
    public $status;     # 201@ $b : aktueller Ausleihstatus
    public $queue;      # 201@ $n : Anzahl Vormerkungen
    public $date;       # 201@ $d : Rückgabedatum

    public $label;      # 209A $a : Signatur
    public $sst;        # 209A $f : Sonderstandort
    public $indikator;  # 209A $d : Ausleihindikator
                        # 0:verfügbar, 1:bestellbar, 6: unbekannt (Bandliste), sonst: nicht verfügbar

    public function setFromField(Field $field) {
        if ($field->tag == '201@') {
            $this->epn       = $field->e;            
            $this->href      = $field->l;
            $this->status    = $field->b;
            $this->date      = $field->d;
            $this->queue     = $field->n; 
        } elseif ($field->tag == '209A') {
            $this->label     = $field->a;
            $this->indikator = $field->d;
            $this->sst       = $field->f;
        }
    }
}
