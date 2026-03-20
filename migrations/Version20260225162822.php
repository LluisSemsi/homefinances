<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225162822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cuenta_bancaria (id INT AUTO_INCREMENT NOT NULL, iban VARCHAR(34) NOT NULL, nombre_banco VARCHAR(100) NOT NULL, titular VARCHAR(100) NOT NULL, alias VARCHAR(50) NOT NULL, saldo NUMERIC(10, 2) NOT NULL, UNIQUE INDEX UNIQ_ECD0C9CEFAD56E62 (iban), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE cuenta_bancaria_user (cuenta_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F294D8B49AEFF118 (cuenta_id), INDEX IDX_F294D8B4A76ED395 (user_id), PRIMARY KEY(cuenta_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE estado_fondo_inversion (id INT AUTO_INCREMENT NOT NULL, fondo_inversion_id INT NOT NULL, fecha DATE NOT NULL, periodicidad VARCHAR(45) NOT NULL, duracion VARCHAR(45) NOT NULL, num_participaciones NUMERIC(10, 2) NOT NULL, precio_medio NUMERIC(10, 2) NOT NULL, ultimo_precio NUMERIC(10, 2) NOT NULL, diferencia NUMERIC(10, 2) NOT NULL, importe_invertido NUMERIC(10, 2) NOT NULL, dividendos NUMERIC(10, 2) NOT NULL, valor_actual NUMERIC(10, 2) NOT NULL, diferencia_total NUMERIC(10, 2) NOT NULL, diferencia_total_percent NUMERIC(6, 4) NOT NULL, INDEX IDX_297A6C73196F9A0 (fondo_inversion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fondo_inversion (id INT AUTO_INCREMENT NOT NULL, producto_inversion_id INT NOT NULL, isin VARCHAR(12) NOT NULL, nombre VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_2EFD1E402FE82D2D (isin), INDEX IDX_2EFD1E404FDA5854 (producto_inversion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE movimiento_bancario (id INT AUTO_INCREMENT NOT NULL, cuenta_id INT NOT NULL, tipo_id INT NOT NULL, cantidad NUMERIC(10, 2) NOT NULL, concepto VARCHAR(255) DEFAULT NULL, fecha DATE NOT NULL, saldo_actual NUMERIC(10, 2) NOT NULL, hash VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_D3E737DFD1B862B8 (hash), INDEX IDX_D3E737DF9AEFF118 (cuenta_id), INDEX IDX_D3E737DFA9276E6C (tipo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE movimiento_inversion (id INT AUTO_INCREMENT NOT NULL, tipo_id INT NOT NULL, producto_inversion_id INT NOT NULL, fecha DATE NOT NULL, num_participaciones NUMERIC(10, 2) NOT NULL, importe_iva NUMERIC(10, 2) NOT NULL, importe_neto_aportado NUMERIC(10, 2) NOT NULL, importe_retencion NUMERIC(10, 2) NOT NULL, comision_movimiento NUMERIC(10, 2) NOT NULL, importe_bruto_aportado NUMERIC(10, 2) NOT NULL, precio_unitario NUMERIC(10, 4) NOT NULL, INDEX IDX_2856BC52A9276E6C (tipo_id), INDEX IDX_2856BC524FDA5854 (producto_inversion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE producto_inversion (id INT AUTO_INCREMENT NOT NULL, cuenta_id INT NOT NULL, num_contrato INT NOT NULL, nombre VARCHAR(100) NOT NULL, total_actual_fondo NUMERIC(10, 2) NOT NULL, total_invertido NUMERIC(10, 2) NOT NULL, diferencia_total NUMERIC(5, 2) NOT NULL, fecha_apertura DATETIME NOT NULL, UNIQUE INDEX UNIQ_749284FBF7A3CE5 (num_contrato), INDEX IDX_749284F9AEFF118 (cuenta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tipo_movimiento_bancario (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tipo_movimiento_inversion (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_86C126253A909126 (nombre), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(25) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cuenta_bancaria_user ADD CONSTRAINT FK_F294D8B49AEFF118 FOREIGN KEY (cuenta_id) REFERENCES cuenta_bancaria (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cuenta_bancaria_user ADD CONSTRAINT FK_F294D8B4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE estado_fondo_inversion ADD CONSTRAINT FK_297A6C73196F9A0 FOREIGN KEY (fondo_inversion_id) REFERENCES fondo_inversion (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fondo_inversion ADD CONSTRAINT FK_2EFD1E404FDA5854 FOREIGN KEY (producto_inversion_id) REFERENCES producto_inversion (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_bancario ADD CONSTRAINT FK_D3E737DF9AEFF118 FOREIGN KEY (cuenta_id) REFERENCES cuenta_bancaria (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_bancario ADD CONSTRAINT FK_D3E737DFA9276E6C FOREIGN KEY (tipo_id) REFERENCES tipo_movimiento_bancario (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion ADD CONSTRAINT FK_2856BC52A9276E6C FOREIGN KEY (tipo_id) REFERENCES tipo_movimiento_inversion (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion ADD CONSTRAINT FK_2856BC524FDA5854 FOREIGN KEY (producto_inversion_id) REFERENCES producto_inversion (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_inversion ADD CONSTRAINT FK_749284F9AEFF118 FOREIGN KEY (cuenta_id) REFERENCES cuenta_bancaria (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cuenta_bancaria_user DROP FOREIGN KEY FK_F294D8B49AEFF118
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cuenta_bancaria_user DROP FOREIGN KEY FK_F294D8B4A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE estado_fondo_inversion DROP FOREIGN KEY FK_297A6C73196F9A0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fondo_inversion DROP FOREIGN KEY FK_2EFD1E404FDA5854
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_bancario DROP FOREIGN KEY FK_D3E737DF9AEFF118
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_bancario DROP FOREIGN KEY FK_D3E737DFA9276E6C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion DROP FOREIGN KEY FK_2856BC52A9276E6C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE movimiento_inversion DROP FOREIGN KEY FK_2856BC524FDA5854
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE producto_inversion DROP FOREIGN KEY FK_749284F9AEFF118
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cuenta_bancaria
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cuenta_bancaria_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE estado_fondo_inversion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fondo_inversion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE movimiento_bancario
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE movimiento_inversion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE producto_inversion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tipo_movimiento_bancario
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tipo_movimiento_inversion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
