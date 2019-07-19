<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190717070910 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cairn_user ADD card_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07154ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('CREATE INDEX IDX_27B07154ACC9A20 ON cairn_user (card_id)');
        $this->addSql('UPDATE cairn_user INNER JOIN card ON cairn_user.id = card.user_id SET cairn_user.card_id = card.id');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3A76ED395');
        $this->addSql('DROP INDEX UNIQ_161498D3A76ED395 ON card');
        $this->addSql('ALTER TABLE card DROP user_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07154ACC9A20');
        $this->addSql('DROP INDEX IDX_27B07154ACC9A20 ON cairn_user');
        $this->addSql('ALTER TABLE cairn_user DROP card_id');
        $this->addSql('ALTER TABLE card ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_161498D3A76ED395 ON card (user_id)');
    }
}
