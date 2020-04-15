<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200415183350 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE push_notification (id INT AUTO_INCREMENT NOT NULL, app_data_id INT DEFAULT NULL, device_token VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, types LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', min_amount INT DEFAULT NULL, radius INT DEFAULT NULL, INDEX IDX_4ABA22EADAA82171 (app_data_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE push_notification ADD CONSTRAINT FK_4ABA22EADAA82171 FOREIGN KEY (app_data_id) REFERENCES app_data (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE push_notification');
    }
}
