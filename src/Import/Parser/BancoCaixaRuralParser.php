<?php

namespace App\Import\Parser;

use App\Import\DTO\MovimientoBancarioDTO;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class BancoCaixaRuralParser implements BankParserInterface
{
    public function parse(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $rows = [];
        $first = true;
        $startReading = false;

        foreach ($sheet->getRowIterator() as $row) {

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $cells = [];

            foreach ($cellIterator as $cell) {
                $cells[] = trim((string) $cell->getValue());
            }

            // Saltar cabecera
            if ($first) {
                $first = false;
                continue;
            }

            // Columnas esperadas:
            // 0: Fecha
            // 1: Fecha Valor
            // 2: Concepto
            // 3: Importe
            // 4: Saldo

            if (empty($cells[0])) {
                continue; // fila vacía
            }


            //DE ACI CAP ABAIX ES EL REALITZAT PER AL EXCEL DE MEDIOLANUM

            if (!$startReading) {
                $value = $cells[0] ?? null; // columna A

                if ($this->isExcelDate($value)) {
                    $startReading = true;
                } else {
                    continue; // saltamos cabeceras
                }
            }

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
            $dto->concepto = $cells[2];

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
