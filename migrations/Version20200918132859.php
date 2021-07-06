<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200918132859 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B4887F3F8');
        $this->addSql('CREATE TABLE adress (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, code_postal VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, type INT NOT NULL, country VARCHAR(255) NOT NULL, INDEX IDX_5CECC7BEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adress ADD CONSTRAINT FK_5CECC7BEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE shipping');
        $this->addSql('DROP INDEX UNIQ_2246507B4887F3F8 ON basket');
        $this->addSql('ALTER TABLE basket CHANGE shipping_id adress_id INT DEFAULT NULL, CHANGE date_shipping date_sending DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B8486F9AC FOREIGN KEY (adress_id) REFERENCES adress (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507B8486F9AC ON basket (adress_id)');
        $this->addSql('ALTER TABLE category DROP INDEX UNIQ_64C19C1B51A1840, ADD INDEX IDX_64C19C1B51A1840 (category_parent_id)');
        $this->addSql('ALTER TABLE product ADD date_add DATETIME NOT NULL, ADD product_condition VARCHAR(255) NOT NULL, ADD exclu_web TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP FOREIGN KEY FK_2246507B8486F9AC');
        $this->addSql('CREATE TABLE shipping (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, last_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, adress VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, city VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, code_postal VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, phone_number VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, type INT NOT NULL, country VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_2D1C1724A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE shipping ADD CONSTRAINT FK_2D1C1724A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE adress');
        $this->addSql('DROP INDEX UNIQ_2246507B8486F9AC ON basket');
        $this->addSql('ALTER TABLE basket CHANGE adress_id shipping_id INT DEFAULT NULL, CHANGE date_sending date_shipping DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B4887F3F8 FOREIGN KEY (shipping_id) REFERENCES shipping (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507B4887F3F8 ON basket (shipping_id)');
        $this->addSql('ALTER TABLE category DROP INDEX IDX_64C19C1B51A1840, ADD UNIQUE INDEX UNIQ_64C19C1B51A1840 (category_parent_id)');
        $this->addSql('ALTER TABLE product DROP date_add, DROP product_condition, DROP exclu_web');
    }
}
