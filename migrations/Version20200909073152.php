<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200909073152 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE shipping (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, INDEX IDX_2D1C1724A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shipping ADD CONSTRAINT FK_2D1C1724A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE basket ADD shipping_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B4887F3F8 FOREIGN KEY (shipping_id) REFERENCES shipping (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507B4887F3F8 ON basket (shipping_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B4887F3F8');
        $this->addSql('DROP TABLE shipping');
        $this->addSql('DROP INDEX UNIQ_2246507B4887F3F8 ON basket');
        $this->addSql('ALTER TABLE basket DROP shipping_id');
    }
}
