<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190408140138 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE deposit (id INT AUTO_INCREMENT NOT NULL, creditor_id INT DEFAULT NULL, status SMALLINT NOT NULL, requestedAt DATETIME NOT NULL, executedAt DATETIME DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, INDEX IDX_95DB9D39DF91AC92 (creditor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE helloasso_conversion (id INT AUTO_INCREMENT NOT NULL, paymentID VARCHAR(255) NOT NULL, amount DOUBLE PRECISION NOT NULL, email VARCHAR(255) NOT NULL, accountNumber VARCHAR(10) NOT NULL, date DATETIME NOT NULL, creditorName VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C706436A4EA83766 (paymentID), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D39DF91AC92 FOREIGN KEY (creditor_id) REFERENCES cairn_user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_27B071544F75D31 ON cairn_user (main_icc)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE deposit');
        $this->addSql('DROP TABLE helloasso_conversion');
        $this->addSql('DROP INDEX UNIQ_27B071544F75D31 ON cairn_user');
    }
}
