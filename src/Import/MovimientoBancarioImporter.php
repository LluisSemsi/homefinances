<?php

namespace App\Import;

use App\Entity\CuentaBancaria;
use App\Import\Parser\BankParserInterface;
use App\Import\Mapper\MovimientoBancarioMapper;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MovimientoBancario;

class MovimientoBancarioImporter
{
    public function __construct(
        private MovimientoBancarioMapper $mapper,
        private EntityManagerInterface $em,
    ) {}

    public function importFromParser(string $filePath, CuentaBancaria $cuenta, BankParserInterface $parser): int
    {
        $dtos = $parser->parse($filePath);

        $count = 0;
        foreach ($dtos as $dto) {
            $mov = $this->mapper->map($dto, $cuenta);

            $existing = $this->em->getRepository(MovimientoBancario::class)
                ->findOneBy(['hash' => $mov->getHash()]);

            if ($existing) {
                continue;
            }

            $this->em->persist($mov);
            $count++;
        }

        if (isset($mov) && $count > 0) {
            $cuenta->setSaldo($mov->getSaldoActual());
            $this->em->persist($cuenta);
        }

        $this->em->flush();

        return $count;
    }
}
