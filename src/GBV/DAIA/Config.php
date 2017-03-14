<?php declare(strict_types=1);

namespace GBV\DAIA;

/** @package GBVDAIA */
interface Config
{
    public function sst(string $isil, string $sst): array;

    // Expected to return array of service to 'is', 'limitation'.
    public function loanIndicator(string $dbkey, string $indikator): array;
}
