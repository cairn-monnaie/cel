<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190527091816 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE api_client (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, login VARCHAR(100) NOT NULL, accessToken VARCHAR(255) NOT NULL, webhook VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_41B343D5AA08CB10 (login), UNIQUE INDEX UNIQ_41B343D5350A9822 (accessToken), UNIQUE INDEX UNIQ_41B343D58A741756 (webhook), UNIQUE INDEX UNIQ_41B343D5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_client ADD CONSTRAINT FK_41B343D5A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE api_client');
    }
}
