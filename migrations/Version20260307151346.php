<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307151346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion DROP FOREIGN KEY FK_2856BC524FDA5854
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2856BC524FDA5854 ON movimiento_inversion
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion CHANGE producto_inversion_id fondo_inversion_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion ADD CONSTRAINT FK_2856BC52196F9A0 FOREIGN KEY (fondo_inversion_id) REFERENCES fondo_inversion (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2856BC52196F9A0 ON movimiento_inversion (fondo_inversion_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion DROP FOREIGN KEY FK_2856BC52196F9A0
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_2856BC52196F9A0 ON movimiento_inversion
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion CHANGE fondo_inversion_id producto_inversion_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion ADD CONSTRAINT FK_2856BC524FDA5854 FOREIGN KEY (producto_inversion_id) REFERENCES producto_inversion (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2856BC524FDA5854 ON movimiento_inversion (producto_inversion_id)
        SQL);
    }
}
