<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918205349 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Rename task table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todos_task_file DROP CONSTRAINT fk_23cc9bb48db60186');

        $this->addSql('
            CREATE TABLE task (
                id UUID NOT NULL, 
                user_id UUID NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                description TEXT DEFAULT NULL, 
                status VARCHAR(50) NOT NULL, 
                deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
                date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                PRIMARY KEY(id)
            )'
        );

        $this->addSql('COMMENT ON COLUMN task.id IS \'UUID type(DC2Type:todos_task_id)\'');
        $this->addSql('COMMENT ON COLUMN task.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN task.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE todos_tasks');
        $this->addSql('ALTER TABLE todos_task_file ADD CONSTRAINT FK_23CC9BB48DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todos_task_file DROP CONSTRAINT FK_23CC9BB48DB60186');

        $this->addSql('
            CREATE TABLE todos_tasks (
                id UUID NOT NULL, 
                user_id UUID NOT NULL, 
                name VARCHAR(255) NOT NULL, 
                description TEXT DEFAULT NULL, 
                status VARCHAR(50) NOT NULL, 
                date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
                PRIMARY KEY(id)
            )'
        );

        $this->addSql('CREATE INDEX idx_bbf64fc1a76ed395 ON todos_tasks (user_id)');
        $this->addSql('COMMENT ON COLUMN todos_tasks.id IS \'UUID type(DC2Type:todos_task_id)\'');
        $this->addSql('COMMENT ON COLUMN todos_tasks.user_id IS \'UUID type(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN todos_tasks.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN todos_tasks.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE todos_tasks ADD CONSTRAINT fk_bbf64fc1a76ed395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE task');
        $this->addSql('ALTER TABLE todos_task_file ADD CONSTRAINT fk_23cc9bb48db60186 FOREIGN KEY (task_id) REFERENCES todos_tasks (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
