<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220104211858 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE todos_task_file (id UUID NOT NULL, task_id UUID NOT NULL, filename VARCHAR(150) NOT NULL, original_filename VARCHAR(150) NOT NULL, filepath VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, mime_type VARCHAR(150) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_23CC9BB48DB60186 ON todos_task_file (task_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23CC9BB43C0BE965 ON todos_task_file (filename)');
        $this->addSql('COMMENT ON COLUMN todos_task_file.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE todos_task_file ADD CONSTRAINT FK_23CC9BB48DB60186 FOREIGN KEY (task_id) REFERENCES todos_tasks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE todos_tasks ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE todos_tasks ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE todos_task_file');
        $this->addSql('ALTER TABLE todos_tasks ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE todos_tasks ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email DROP DEFAULT');
    }
}
