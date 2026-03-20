<?php

namespace App\Import\Parser;

use App\Import\DTO\MovimientoBancarioDTO;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class BancoSabadellParser implements BankParserInterface
{
    public function parse(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = [];
        $first = false;
        $startReading = false;

        foreach ($sheet->getRowIterator() as $row) {

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];

            foreach ($cellIterator as $cell) {
                $cells[] = trim((string) $cell->getValue());
            }


            // Columnas esperadas:
            // 0: F.operativa
            // 1: Concepto
            // 2: Fecha Valor
            // 3: Importe
            // 4: Saldo

            if($cells[0] == 'F. Operativa'){
                $first = true;
                continue;
            }

            if(!$first)
                continue;

            $dto = new MovimientoBancarioDTO();

            // Fecha
            $value = $cells[0];

            // Caso 1: Excel devuelve un número (fecha serial)
            if (is_numeric($value)) {
                $dto->fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            // Caso 2: Excel devuelve un DateTime directamente
            elseif ($value instanceof \DateTimeInterface) {
                $dto->fecha = $value;
            }
            // Caso 3: Excel devuelve un string
            else {
                $fecha = \DateTime::createFromFormat('d/m/Y', $value);

                if (!$fecha) {
                    throw new \RuntimeException("Fecha inválida en Mediolanum: '$value'");
                }

                $dto->fecha = $fecha;
            }

            // Concepto
            $dto->concepto = $cells[1];

            $dto->cantidad = $cells[3];

            // Saldo
            $dto->saldoActual = (float) str_replace(',', '.', $cells[4]);

            // Tipo
            $dto->tipoNombre = $dto->cantidad >= 0 ? 'INGRESO' : 'GASTO';

            $rows[] = $dto;
        }


        return $rows;

    }

    function isExcelDate($value): bool
    {
        if ($value instanceof \DateTimeInterface) {
            return true;
        }

        // Excel date serial (número)
        if (is_numeric($value)) {
            try {
                Date::excelToDateTimeObject($value);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        // Fecha en texto
        if (is_string($value)) {
            return strtotime($value) !== false;
        }

        return false;
    }
}
