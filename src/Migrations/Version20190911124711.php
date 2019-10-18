<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190911124711 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mandate (id INT AUTO_INCREMENT NOT NULL, contractor_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, beginAt DATETIME NOT NULL, endAt DATETIME NOT NULL, createdAt DATETIME NOT NULL, status SMALLINT NOT NULL, INDEX IDX_197D0FEEB0265DC7 (contractor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mandate ADD CONSTRAINT FK_197D0FEEB0265DC7 FOREIGN KEY (contractor_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE operation ADD mandate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D6C1129CD FOREIGN KEY (mandate_id) REFERENCES mandate (id)');
        $this->addSql('CREATE INDEX IDX_1981A66D6C1129CD ON operation (mandate_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D6C1129CD');
        $this->addSql('DROP TABLE mandate');
        $this->addSql('DROP INDEX IDX_1981A66D6C1129CD ON operation');
        $this->addSql('ALTER TABLE operation DROP mandate_id');
    }
}
