<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201007112654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE slide (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, theme_id INT DEFAULT NULL, page_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_72EFEE624584665A (product_id), UNIQUE INDEX UNIQ_72EFEE6259027487 (theme_id), UNIQUE INDEX UNIQ_72EFEE62C4663E4 (page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE624584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE6259027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE slide ADD CONSTRAINT FK_72EFEE62C4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE slide');
    }
}
