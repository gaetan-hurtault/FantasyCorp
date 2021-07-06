<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201007121352 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slide ADD picture_id INT NOT NULL, ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE62EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72EFEE62EE45BDBF ON slide (picture_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slide DROP FOREIGN KEY FK_72EFEE62EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_72EFEE62EE45BDBF ON slide');
        $this->addSql('ALTER TABLE slide DROP picture_id, DROP name');
    }
}
