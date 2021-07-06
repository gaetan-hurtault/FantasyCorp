<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201005065142 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture ADD theme_id INT DEFAULT NULL, CHANGE product_id product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8959027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16DB4F8959027487 ON picture (theme_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8959027487');
        $this->addSql('DROP INDEX UNIQ_16DB4F8959027487 ON picture');
        $this->addSql('ALTER TABLE picture DROP theme_id, CHANGE product_id product_id INT NOT NULL');
    }
}
