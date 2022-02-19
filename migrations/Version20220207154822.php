<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220207154822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, archived INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commune (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, tarif NUMERIC(10, 0) NOT NULL, montant_max NUMERIC(10, 0) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_fix VARCHAR(255) NOT NULL, particular_info VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordered (id INT AUTO_INCREMENT NOT NULL, commune_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, quantity NUMERIC(10, 0) NOT NULL, numero_order VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, status_ordered INT NOT NULL, total_ordered NUMERIC(10, 0) NOT NULL, INDEX IDX_C3121F99131A4F72 (commune_id), INDEX IDX_C3121F999395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ordered_detail (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, ordered_id INT DEFAULT NULL, quantity NUMERIC(10, 0) NOT NULL, date_order DATETIME NOT NULL, INDEX IDX_213D9EBB4584665A (product_id), INDEX IDX_213D9EBBAA60395A (ordered_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, price NUMERIC(10, 0) NOT NULL, archived INT NOT NULL, created_at DATETIME NOT NULL, photo VARCHAR(255) NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_C3121F99131A4F72 FOREIGN KEY (commune_id) REFERENCES commune (id)');
        $this->addSql('ALTER TABLE ordered ADD CONSTRAINT FK_C3121F999395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE ordered_detail ADD CONSTRAINT FK_213D9EBB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE ordered_detail ADD CONSTRAINT FK_213D9EBBAA60395A FOREIGN KEY (ordered_id) REFERENCES ordered (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F99131A4F72');
        $this->addSql('ALTER TABLE ordered DROP FOREIGN KEY FK_C3121F999395C3F3');
        $this->addSql('ALTER TABLE ordered_detail DROP FOREIGN KEY FK_213D9EBBAA60395A');
        $this->addSql('ALTER TABLE ordered_detail DROP FOREIGN KEY FK_213D9EBB4584665A');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE commune');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE ordered');
        $this->addSql('DROP TABLE ordered_detail');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE user');
    }
}
