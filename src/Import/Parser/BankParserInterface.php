<?php

namespace App\Import\Parser;

use App\Import\DTO\MovimientoBancarioDTO;

interface BankParserInterface
{
    /**
     * @return MovimientoBancarioDTO[]
     */
    public function parse(string $filePath): array;
}
