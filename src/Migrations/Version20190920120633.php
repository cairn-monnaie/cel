<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190920120633 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE File ADD mandate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE File ADD CONSTRAINT FK_2CAD992E6C1129CD FOREIGN KEY (mandate_id) REFERENCES mandate (id)');
        $this->addSql('CREATE INDEX IDX_2CAD992E6C1129CD ON File (mandate_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE File DROP FOREIGN KEY FK_2CAD992E6C1129CD');
        $this->addSql('DROP INDEX IDX_2CAD992E6C1129CD ON File');
        $this->addSql('ALTER TABLE File DROP mandate_id');
    }
}
