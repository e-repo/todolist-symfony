<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023124235 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Rename user_user_images and user_user_networks';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE user_image (
                id UUID NOT NULL, 
                user_id UUID NOT NULL, 
                filename VARCHAR(150) NOT NULL, 
                original_filename VARCHAR(150) NOT NULL, 
                filepath VARCHAR(255) NOT NULL, 
                is_active BOOLEAN NOT NULL, 
                mime_type VARCHAR(150) DEFAULT NULL, 
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('CREATE INDEX IDX_27FFFF07A76ED395 ON user_image (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27FFFF073C0BE965 ON user_image (filename)');
        $this->addSql('COMMENT ON COLUMN user_image.user_id IS \'UUID type(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_image.created_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('
            CREATE TABLE user_network (
                id UUID NOT NULL, 
                user_id UUID NOT NULL, 
                network VARCHAR(50) NOT NULL, 
                identity VARCHAR(50) DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX IDX_388999B6A76ED395 ON user_network (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_388999B6608487BC6A95E9C4 ON user_network (network, identity)');
        $this->addSql('COMMENT ON COLUMN user_network.user_id IS \'UUID type(DC2Type:user_id)\'');

        $this->addSql('ALTER TABLE user_image ADD CONSTRAINT FK_27FFFF07A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_network ADD CONSTRAINT FK_388999B6A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE user_user_networks');
        $this->addSql('DROP TABLE user_user_images');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('
            CREATE TABLE user_user_networks (
                id UUID NOT NULL, 
                user_id UUID NOT NULL, 
                network VARCHAR(50) NOT NULL, 
                identity VARCHAR(50) DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX idx_d7bafd7ba76ed395 ON user_user_networks (user_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_d7bafd7b608487bc6a95e9c4 ON user_user_networks (network, identity)');
        $this->addSql('COMMENT ON COLUMN user_user_networks.user_id IS \'UUID type(DC2Type:user_id)\'');

        $this->addSql('
            CREATE TABLE user_user_images (
                id UUID NOT NULL, 
                user_id UUID NOT NULL, 
                filename VARCHAR(150) NOT NULL, 
                original_filename VARCHAR(150) NOT NULL, 
                mime_type VARCHAR(150) DEFAULT NULL, 
                created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                filepath VARCHAR(255) NOT NULL, 
                is_active BOOLEAN NOT NULL, 
                PRIMARY KEY(id)
            )
        ');
        $this->addSql('CREATE INDEX idx_2c566cc2a76ed395 ON user_user_images (user_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_2c566cc23c0be965 ON user_user_images (filename)');
        $this->addSql('COMMENT ON COLUMN user_user_images.user_id IS \'UUID type(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_user_images.created_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('ALTER TABLE user_user_networks ADD CONSTRAINT fk_d7bafd7ba76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user_images ADD CONSTRAINT fk_2c566cc2a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE user_image');
        $this->addSql('DROP TABLE user_network');
    }
}
