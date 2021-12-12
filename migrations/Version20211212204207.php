<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211212204207 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_user_images (id UUID NOT NULL, user_id UUID NOT NULL, filename VARCHAR(150) NOT NULL, original_filename VARCHAR(150) NOT NULL, mime_type VARCHAR(150) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C566CC2A76ED395 ON user_user_images (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2C566CC23C0BE965 ON user_user_images (filename)');
        $this->addSql('COMMENT ON COLUMN user_user_images.user_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_user_images.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_user_images ADD CONSTRAINT FK_2C566CC2A76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE todos_tasks ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE todos_tasks ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_user_networks.user_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('ALTER TABLE user_users ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'(DC2Type:user_user_id)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE user_user_images');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_user_networks.user_id IS NULL');
        $this->addSql('ALTER TABLE todos_tasks ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE todos_tasks ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_users.id IS NULL');
    }
}
