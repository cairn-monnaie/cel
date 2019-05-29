<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190528162507 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE online_payment (id INT AUTO_INCREMENT NOT NULL, urlSuccess VARCHAR(255) NOT NULL, urlFailure VARCHAR(255) NOT NULL, accountNumber VARCHAR(15) NOT NULL, amount DOUBLE PRECISION NOT NULL, invoiceID VARCHAR(255) NOT NULL, reason VARCHAR(255) NOT NULL, urlValidationSuffix VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5CBE57F3615FC99 (invoiceID), UNIQUE INDEX UNIQ_5CBE57F39DBE7E38 (urlValidationSuffix), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE online_payment');
    }
}
