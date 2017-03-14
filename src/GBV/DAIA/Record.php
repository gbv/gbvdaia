<?php declare(strict_types=1);

namespace GBV\DAIA;

use PICA\Field;

/**
 * PICA+ record stripped down to information required for DAIA.
 *
 * @package GBVDAIA
 */
class Record
{
    public $mak;
    public $holdings = [];

    /**
     * Parse plain PICA+.
     */
    public function __construct(string $pica)
    {
        $fields = explode("\n", $pica);
        $fields = array_values(preg_grep('/^(002@|101@|201@|209A)/', $fields));

        $holdings = [];
        foreach ($fields as $field) {
            $field = new Field($field);

            if ($field->tag == '002@') {
                $this->mak = $field->value('0');
            } elseif ($field->tag == '101@') {
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
                $holding->setFromField($field);
            }
        }
    }
}
