<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191016141812 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE account_score (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, format VARCHAR(5) NOT NULL, email VARCHAR(255) NOT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, schedule LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', nb_sent_today SMALLINT NOT NULL, considered_day VARCHAR(4) NOT NULL, UNIQUE INDEX UNIQ_416EE20E7927C74 (email), UNIQUE INDEX UNIQ_416EE20C05FB297 (confirmation_token), UNIQUE INDEX UNIQ_416EE20A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account_score ADD CONSTRAINT FK_416EE20A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE account_score');
    }
}
