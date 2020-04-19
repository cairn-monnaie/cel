<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200418164353 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE push_notification DROP FOREIGN KEY FK_4ABA22EADAA82171');
        $this->addSql('ALTER TABLE user_sms_data DROP FOREIGN KEY FK_F6538B35A86BC603');
        $this->addSql('CREATE TABLE base_notification (id INT AUTO_INCREMENT NOT NULL, notification_data_id INT DEFAULT NULL, web_push_enabled TINYINT(1) NOT NULL, app_push_enabled TINYINT(1) NOT NULL, sms_enabled TINYINT(1) NOT NULL, email_enabled TINYINT(1) NOT NULL, discr VARCHAR(255) NOT NULL, types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', min_amount INT DEFAULT NULL, radius INT DEFAULT NULL, INDEX IDX_C3B291222EB2B8B (notification_data_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_data (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, pin_code VARCHAR(10) DEFAULT NULL, device_tokens LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_15CFB859A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE web_push_subscription (id INT AUTO_INCREMENT NOT NULL, notification_data_id INT DEFAULT NULL, endpoint VARCHAR(255) NOT NULL, encryption_keys LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_E60ECC87C4420F7B (endpoint), INDEX IDX_E60ECC8722EB2B8B (notification_data_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE base_notification ADD CONSTRAINT FK_C3B291222EB2B8B FOREIGN KEY (notification_data_id) REFERENCES notification_data (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification_data ADD CONSTRAINT FK_15CFB859A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE web_push_subscription ADD CONSTRAINT FK_E60ECC8722EB2B8B FOREIGN KEY (notification_data_id) REFERENCES notification_data (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE app_data');
        $this->addSql('DROP TABLE notification_permission');
        $this->addSql('DROP TABLE push_notification');
        $this->addSql('DROP INDEX UNIQ_F6538B35A86BC603 ON user_sms_data');
        $this->addSql('ALTER TABLE user_sms_data DROP notification_permission_id, DROP webPush_subscriptions');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE base_notification DROP FOREIGN KEY FK_C3B291222EB2B8B');
        $this->addSql('ALTER TABLE web_push_subscription DROP FOREIGN KEY FK_E60ECC8722EB2B8B');
        $this->addSql('CREATE TABLE app_data (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, pinCode SMALLINT DEFAULT NULL, firstLogin TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_A8DDD6C3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE notification_permission (id INT AUTO_INCREMENT NOT NULL, emailEnabled TINYINT(1) NOT NULL, webPushEnabled TINYINT(1) NOT NULL, smsEnabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE push_notification (id INT AUTO_INCREMENT NOT NULL, app_data_id INT DEFAULT NULL, device_token VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, discr VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, types LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', min_amount INT DEFAULT NULL, radius INT DEFAULT NULL, INDEX IDX_4ABA22EADAA82171 (app_data_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE app_data ADD CONSTRAINT FK_A8DDD6C3A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE push_notification ADD CONSTRAINT FK_4ABA22EADAA82171 FOREIGN KEY (app_data_id) REFERENCES app_data (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE base_notification');
        $this->addSql('DROP TABLE notification_data');
        $this->addSql('DROP TABLE web_push_subscription');
        $this->addSql('ALTER TABLE user_sms_data ADD notification_permission_id INT DEFAULT NULL, ADD webPush_subscriptions LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE user_sms_data ADD CONSTRAINT FK_F6538B35A86BC603 FOREIGN KEY (notification_permission_id) REFERENCES notification_permission (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6538B35A86BC603 ON user_sms_data (notification_permission_id)');
    }
}
