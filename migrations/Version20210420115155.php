<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210420115155 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image ADD task_id_id INT NOT NULL, DROP image_file_name');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045FB8E08577 FOREIGN KEY (task_id_id) REFERENCES task (id)');
        $this->addSql('CREATE INDEX IDX_C53D045FB8E08577 ON image (task_id_id)');
        $this->addSql('ALTER TABLE task DROP image_file_name');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045FB8E08577');
        $this->addSql('DROP INDEX IDX_C53D045FB8E08577 ON image');
        $this->addSql('ALTER TABLE image ADD image_file_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP task_id_id');
        $this->addSql('ALTER TABLE task ADD image_file_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
