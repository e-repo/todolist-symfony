<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221022095356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Rename task file table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE task_file (
                id UUID NOT NULL, 
                task_id UUID NOT NULL, 
                filename VARCHAR(150) NOT NULL, 
                original_filename VARCHAR(150) NOT NULL, 
                filepath VARCHAR(255) NOT NULL, 
                is_active BOOLEAN NOT NULL, 
                mime_type VARCHAR(150) DEFAULT NULL, 
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('CREATE INDEX IDX_FF2CA26B8DB60186 ON task_file (task_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FF2CA26B3C0BE965 ON task_file (filename)');
        $this->addSql('COMMENT ON COLUMN task_file.task_id IS \'UUID type(DC2Type:todos_task_id)\'');
        $this->addSql('COMMENT ON COLUMN task_file.created_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('ALTER TABLE task_file ADD CONSTRAINT FK_FF2CA26B8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('DROP TABLE todos_task_file');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE todos_task_file (
                id UUID NOT NULL, 
                task_id UUID NOT NULL, 
                filename VARCHAR(150) NOT NULL, 
                original_filename VARCHAR(150) NOT NULL, 
                filepath VARCHAR(255) NOT NULL, 
                is_active BOOLEAN NOT NULL, 
                mime_type VARCHAR(150) DEFAULT NULL, 
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('CREATE INDEX idx_23cc9bb48db60186 ON todos_task_file (task_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_23cc9bb43c0be965 ON todos_task_file (filename)');
        $this->addSql('COMMENT ON COLUMN todos_task_file.task_id IS \'UUID type(DC2Type:todos_task_id)\'');
        $this->addSql('COMMENT ON COLUMN todos_task_file.created_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('ALTER TABLE todos_task_file ADD CONSTRAINT fk_23cc9bb48db60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql('DROP TABLE task_file');
    }
}
