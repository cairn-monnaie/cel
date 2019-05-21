<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190521074902 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE notification_permission (id INT AUTO_INCREMENT NOT NULL, emailEnabled TINYINT(1) NOT NULL, webPushEnabled TINYINT(1) NOT NULL, smsEnabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone (id INT AUTO_INCREMENT NOT NULL, sms_data_id INT NOT NULL, phoneNumber VARCHAR(15) NOT NULL, identifier VARCHAR(30) DEFAULT NULL, payment_enabled TINYINT(1) NOT NULL, dailyAmountThreshold INT NOT NULL, dailyNumberPaymentsThreshold INT NOT NULL, UNIQUE INDEX UNIQ_444F97DD772E836A (identifier), INDEX IDX_444F97DDC7EF6AE6 (sms_data_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DDC7EF6AE6 FOREIGN KEY (sms_data_id) REFERENCES user_sms_data (id)');
        $this->addSql('DROP INDEX UNIQ_27B0715C86334A2 ON cairn_user');
        $this->addSql('ALTER TABLE cairn_user DROP smsClient, DROP webpush_endpoints');
        $this->addSql('ALTER TABLE user_sms_data DROP INDEX IDX_F6538B35A76ED395, ADD UNIQUE INDEX UNIQ_F6538B35A76ED395 (user_id)');
        $this->addSql('DROP INDEX UNIQ_F6538B35772E836A ON user_sms_data');
        $this->addSql('ALTER TABLE user_sms_data ADD notification_permission_id INT DEFAULT NULL, ADD smsClient VARCHAR(255) DEFAULT NULL, ADD webPush_endpoints LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP phoneNumber, DROP smsEnabled, DROP dailyAmountThreshold, DROP dailyNumberPaymentsThreshold, DROP identifier, DROP payment_enabled');
        $this->addSql('ALTER TABLE user_sms_data ADD CONSTRAINT FK_F6538B35A86BC603 FOREIGN KEY (notification_permission_id) REFERENCES notification_permission (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6538B35C86334A2 ON user_sms_data (smsClient)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6538B35A86BC603 ON user_sms_data (notification_permission_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_sms_data DROP FOREIGN KEY FK_F6538B35A86BC603');
        $this->addSql('DROP TABLE notification_permission');
        $this->addSql('DROP TABLE phone');
        $this->addSql('ALTER TABLE cairn_user ADD smsClient VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, ADD webpush_endpoints LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27B0715C86334A2 ON cairn_user (smsClient)');
        $this->addSql('ALTER TABLE user_sms_data DROP INDEX UNIQ_F6538B35A76ED395, ADD INDEX IDX_F6538B35A76ED395 (user_id)');
        $this->addSql('DROP INDEX UNIQ_F6538B35C86334A2 ON user_sms_data');
        $this->addSql('DROP INDEX UNIQ_F6538B35A86BC603 ON user_sms_data');
        $this->addSql('ALTER TABLE user_sms_data ADD phoneNumber VARCHAR(15) NOT NULL COLLATE utf8_unicode_ci, ADD smsEnabled TINYINT(1) NOT NULL, ADD dailyAmountThreshold INT NOT NULL, ADD dailyNumberPaymentsThreshold INT NOT NULL, ADD identifier VARCHAR(30) DEFAULT NULL COLLATE utf8_unicode_ci, ADD payment_enabled TINYINT(1) NOT NULL, DROP notification_permission_id, DROP smsClient, DROP webPush_endpoints');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6538B35772E836A ON user_sms_data (identifier)');
    }
}
