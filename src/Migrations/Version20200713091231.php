<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200713091231 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cairn_user ADD dolibarr_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27B0715DA308422 ON cairn_user (dolibarr_id)');
        $this->addSql('ALTER TABLE cairn_user CHANGE cyclos_id cyclos_id BIGINT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_27B0715DA308422 ON cairn_user');
        $this->addSql('ALTER TABLE cairn_user DROP dolibarr_id');
        $this->addSql('ALTER TABLE cairn_user CHANGE cyclos_id cyclos_id BIGINT NOT NULL');

    }
}
