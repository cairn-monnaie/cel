<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190326082024 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cairn_user ADD smsClient VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27B071544F75D31 ON cairn_user (main_icc)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27B0715C86334A2 ON cairn_user (smsClient)');
        $this->addSql('ALTER TABLE user_sms_data DROP INDEX UNIQ_F6538B35A76ED395, ADD INDEX IDX_F6538B35A76ED395 (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_27B071544F75D31 ON cairn_user');
        $this->addSql('DROP INDEX UNIQ_27B0715C86334A2 ON cairn_user');
        $this->addSql('ALTER TABLE cairn_user DROP smsClient');
        $this->addSql('ALTER TABLE user_sms_data DROP INDEX IDX_F6538B35A76ED395, ADD UNIQUE INDEX UNIQ_F6538B35A76ED395 (user_id)');
    }
}
