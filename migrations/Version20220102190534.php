<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220102190534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, cost NUMERIC(7, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipping (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, cost NUMERIC(7, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD shipping_id INT DEFAULT NULL, ADD payment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984887F3F8 FOREIGN KEY (shipping_id) REFERENCES shipping (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('CREATE INDEX IDX_F52993984887F3F8 ON `order` (shipping_id)');
        $this->addSql('CREATE INDEX IDX_F52993984C3A3BB ON `order` (payment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984C3A3BB');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984887F3F8');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE shipping');
        $this->addSql('DROP INDEX IDX_F52993984887F3F8 ON `order`');
        $this->addSql('DROP INDEX IDX_F52993984C3A3BB ON `order`');
        $this->addSql('ALTER TABLE `order` DROP shipping_id, DROP payment_id');
    }
}
