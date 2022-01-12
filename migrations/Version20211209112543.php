<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211209112543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_fix VARCHAR(255) NOT NULL, particular_info VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ordered ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_C3121F999395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_C3121F999395C3F3 ON ordered (customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F999395C3F3');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP INDEX IDX_C3121F999395C3F3 ON ordered');
        $this->addSql('ALTER TABLE ordered DROP customer_id');
    }
}
