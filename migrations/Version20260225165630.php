<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225165630 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_inversion DROP total_actual_fondo, DROP total_invertido, DROP diferencia_total, CHANGE fecha_apertura fecha_apertura DATE NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_inversion ADD total_actual_fondo NUMERIC(10, 2) NOT NULL, ADD total_invertido NUMERIC(10, 2) NOT NULL, ADD diferencia_total NUMERIC(5, 2) NOT NULL, CHANGE fecha_apertura fecha_apertura DATETIME NOT NULL
        SQL);
    }
}
