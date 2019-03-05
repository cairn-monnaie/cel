<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190305134515_identity_document_id extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07153DA5256D');
        $this->addSql('CREATE TABLE File (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE image');
        $this->addSql('ALTER TABLE cairn_user ADD identity_document_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07157C97FC13 FOREIGN KEY (identity_document_id) REFERENCES File (id)');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07153DA5256D FOREIGN KEY (image_id) REFERENCES File (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27B07157C97FC13 ON cairn_user (identity_document_id)');
        $this->addSql('ALTER TABLE user_sms_data ADD identifier VARCHAR(30) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07153DA5256D');
        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07157C97FC13');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, alt VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE File');
        $this->addSql('DROP INDEX UNIQ_27B07157C97FC13 ON cairn_user');
        $this->addSql('ALTER TABLE cairn_user DROP identity_document_id');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07153DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE user_sms_data DROP identifier');
    }
}
