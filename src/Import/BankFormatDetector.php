<?php

namespace App\Import;

class BankFormatDetector
{
    public function detect(string $filePath): string
    {
        $filename = strtolower(basename($filePath));

        if (str_contains($filename, 'mediolanum')) {
            return 'mediolanum';
        }

        // aquí luego añadiremos más bancos
        throw new \RuntimeException('No se ha podido detectar el banco para el fichero: '.$filename);
    }
}