<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220918093143 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todos_task_file ALTER task_id TYPE UUID');
        $this->addSql('ALTER TABLE todos_task_file ALTER task_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN todos_task_file.task_id IS \'UUID type(DC2Type:todos_task_id)\'');
        $this->addSql('ALTER TABLE todos_tasks ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE todos_tasks ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN todos_tasks.id IS \'UUID type(DC2Type:todos_task_id)\'');
        $this->addSql('COMMENT ON COLUMN todos_tasks.user_id IS \'UUID type(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_user_images.user_id IS \'UUID type(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_user_networks.user_id IS \'UUID type(DC2Type:user_user_id)\'');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'UUID type(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_users.role IS \'Role type (e.g. ROLE_USER, ROLE_ADMIN)(DC2Type:user_user_role)\'');
        $this->addSql('COMMENT ON COLUMN user_users.email IS \'Email type(DC2Type:user_user_email)\'');
        $this->addSql('COMMENT ON COLUMN user_users.new_email IS \'Email type(DC2Type:user_user_email)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('COMMENT ON COLUMN user_user_networks.user_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('ALTER TABLE todos_tasks ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE todos_tasks ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN todos_tasks.id IS NULL');
        $this->addSql('COMMENT ON COLUMN todos_tasks.user_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN user_users.id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('COMMENT ON COLUMN user_users.role IS NULL');
        $this->addSql('COMMENT ON COLUMN user_users.email IS NULL');
        $this->addSql('COMMENT ON COLUMN user_users.new_email IS NULL');
        $this->addSql('COMMENT ON COLUMN user_user_images.user_id IS \'(DC2Type:user_user_id)\'');
        $this->addSql('ALTER TABLE todos_task_file ALTER task_id TYPE UUID');
        $this->addSql('ALTER TABLE todos_task_file ALTER task_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN todos_task_file.task_id IS NULL');
    }
}
