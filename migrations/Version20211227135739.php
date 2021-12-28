<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211227135739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADD9F966B');
        $this->addSql('DROP INDEX UNIQ_D34A04ADD9F966B ON product');
        $this->addSql('ALTER TABLE product DROP description_id');
        $this->addSql('ALTER TABLE product_description ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE product_description ADD CONSTRAINT FK_C1CBDE394584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C1CBDE394584665A ON product_description (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD description_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADD9F966B FOREIGN KEY (description_id) REFERENCES product_description (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04ADD9F966B ON product (description_id)');
        $this->addSql('ALTER TABLE product_description DROP FOREIGN KEY FK_C1CBDE394584665A');
        $this->addSql('DROP INDEX UNIQ_C1CBDE394584665A ON product_description');
        $this->addSql('ALTER TABLE product_description DROP product_id');
    }
}
