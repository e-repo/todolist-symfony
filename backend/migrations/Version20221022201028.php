<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221022201028 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Rename user table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_user_networks DROP CONSTRAINT IF EXISTS fk_d7bafd7ba76ed395');
        $this->addSql('ALTER TABLE user_user_images DROP CONSTRAINT IF EXISTS fk_2c566cc2a76ed395');

        $this->addSql('
            CREATE TABLE "user" (
                id UUID NOT NULL, 
                date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                role VARCHAR(255) NOT NULL, 
                email VARCHAR(255) DEFAULT NULL, 
                password_hash VARCHAR(255) DEFAULT NULL, 
                confirm_token VARCHAR(255) DEFAULT NULL, 
                new_email VARCHAR(255) DEFAULT NULL, 
                new_email_token VARCHAR(255) DEFAULT NULL, 
                status VARCHAR(20) NOT NULL, 
                name_first VARCHAR(255) NOT NULL, 
                name_last VARCHAR(255) NOT NULL, 
                reset_token VARCHAR(255) DEFAULT NULL, 
                reset_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D7C8DC19 ON "user" (reset_token)');

        $this->addSql('COMMENT ON COLUMN "user".id IS \'UUID type(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN "user_user_networks".user_id IS \'UUID type(DC2Type:user_id)\'');
        $this->addSql('COMMENT ON COLUMN "user_user_images".user_id IS \'UUID type(DC2Type:user_id)\'');

        $this->addSql('COMMENT ON COLUMN "user".date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "user".role IS \'Role type (e.g. ROLE_USER, ROLE_ADMIN)(DC2Type:user_role)\'');
        $this->addSql('COMMENT ON COLUMN "user".email IS \'Email type(DC2Type:user_email)\'');
        $this->addSql('COMMENT ON COLUMN "user".new_email IS \'Email type(DC2Type:user_email)\'');
        $this->addSql('COMMENT ON COLUMN "user".reset_token_expires IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('INSERT INTO "user" 
            SELECT "id", "date", "role", "email", "password_hash", "confirm_token", "new_email", "new_email_token", "status", "name_first", "name_last", "reset_token", "reset_token_expires" FROM "user_users"
        ');

        $this->addSql('DROP TABLE user_users');

        $this->addSql('ALTER TABLE user_user_images DROP CONSTRAINT IF EXISTS FK_2C566CC2A76ED395');
        $this->addSql('ALTER TABLE user_user_images ADD CONSTRAINT FK_2C566CC2A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user_networks DROP CONSTRAINT IF EXISTS FK_D7BAFD7BA76ED395');
        $this->addSql('ALTER TABLE user_user_networks ADD CONSTRAINT FK_D7BAFD7BA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_user_images DROP CONSTRAINT IF EXISTS FK_2C566CC2A76ED395');
        $this->addSql('ALTER TABLE user_user_networks DROP CONSTRAINT IF EXISTS FK_D7BAFD7BA76ED395');

        $this->addSql('
            CREATE TABLE user_users (
                id UUID NOT NULL, 
                date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, 
                role VARCHAR(255) NOT NULL, 
                email VARCHAR(255) DEFAULT NULL, 
                password_hash VARCHAR(255) DEFAULT NULL, 
                confirm_token VARCHAR(255) DEFAULT NULL, 
                status VARCHAR(20) NOT NULL, 
                reset_token VARCHAR(255) DEFAULT NULL, 
                reset_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, 
                new_email VARCHAR(255) DEFAULT NULL, 
                new_email_token VARCHAR(255) DEFAULT NULL, 
                name_first VARCHAR(255) NOT NULL, 
                name_last VARCHAR(255) NOT NULL, 
                PRIMARY KEY(id)
            )
        ');

        $this->addSql('CREATE UNIQUE INDEX uniq_f6415eb1e7927c74 ON user_users (email)');
        $this->addSql('CREATE UNIQUE INDEX uniq_f6415eb1d7c8dc19 ON user_users (reset_token)');

        $this->addSql('COMMENT ON COLUMN user_users.id IS \'UUID type(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_users.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.role IS \'Role type (e.g. ROLE_USER, ROLE_ADMIN)(DC2Type:user_user_role)\'');
        $this->addSql('COMMENT ON COLUMN user_users.email IS \'Email type(DC2Type:user_user_email)\'');
        $this->addSql('COMMENT ON COLUMN user_users.reset_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.new_email IS \'Email type(DC2Type:user_user_email)\'');

        $this->addSql('INSERT INTO "user_users" 
            SELECT "id", "date", "role", "email", "password_hash", "confirm_token", "status", "reset_token", "reset_token_expires", "new_email", "new_email_token", "name_first", "name_last" FROM "user"
        ');

        $this->addSql('DROP TABLE "user"');

        $this->addSql('ALTER TABLE user_user_networks DROP CONSTRAINT IF EXISTS fk_d7bafd7ba76ed395');
        $this->addSql('ALTER TABLE user_user_networks ADD CONSTRAINT fk_d7bafd7ba76ed395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_user_images DROP CONSTRAINT IF EXISTS fk_2c566cc2a76ed395');
        $this->addSql('ALTER TABLE user_user_images ADD CONSTRAINT fk_2c566cc2a76ed395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
