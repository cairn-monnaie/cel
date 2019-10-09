<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191009185656 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07153DA5256D');
        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07154ACC9A20');
        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07157C97FC13');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07153DA5256D FOREIGN KEY (image_id) REFERENCES File (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07154ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07157C97FC13 FOREIGN KEY (identity_document_id) REFERENCES File (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446AA76ED395');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446AA76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DDC7EF6AE6');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DDC7EF6AE6 FOREIGN KEY (sms_data_id) REFERENCES user_sms_data (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D39DF91AC92');
        $this->addSql('ALTER TABLE deposit CHANGE creditor_id creditor_id INT NOT NULL');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D39DF91AC92 FOREIGN KEY (creditor_id) REFERENCES cairn_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE File DROP FOREIGN KEY FK_2CAD992E6C1129CD');
        $this->addSql('ALTER TABLE File ADD CONSTRAINT FK_2CAD992E6C1129CD FOREIGN KEY (mandate_id) REFERENCES mandate (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE api_client DROP FOREIGN KEY FK_41B343D5A76ED395');
        $this->addSql('ALTER TABLE api_client ADD CONSTRAINT FK_41B343D5A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE mandate DROP FOREIGN KEY FK_197D0FEEB0265DC7');
        $this->addSql('ALTER TABLE mandate ADD CONSTRAINT FK_197D0FEEB0265DC7 FOREIGN KEY (contractor_id) REFERENCES cairn_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D72757D19');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DDF91AC92');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D72757D19 FOREIGN KEY (debitor_id) REFERENCES cairn_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66DDF91AC92 FOREIGN KEY (creditor_id) REFERENCES cairn_user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE File DROP FOREIGN KEY FK_2CAD992E6C1129CD');
        $this->addSql('ALTER TABLE File ADD CONSTRAINT FK_2CAD992E6C1129CD FOREIGN KEY (mandate_id) REFERENCES mandate (id)');
        $this->addSql('ALTER TABLE api_client DROP FOREIGN KEY FK_41B343D5A76ED395');
        $this->addSql('ALTER TABLE api_client ADD CONSTRAINT FK_41B343D5A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446AA76ED395');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446AA76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07153DA5256D');
        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07157C97FC13');
        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07154ACC9A20');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07153DA5256D FOREIGN KEY (image_id) REFERENCES File (id)');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07157C97FC13 FOREIGN KEY (identity_document_id) REFERENCES File (id)');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07154ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D39DF91AC92');
        $this->addSql('ALTER TABLE deposit CHANGE creditor_id creditor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D39DF91AC92 FOREIGN KEY (creditor_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE mandate DROP FOREIGN KEY FK_197D0FEEB0265DC7');
        $this->addSql('ALTER TABLE mandate ADD CONSTRAINT FK_197D0FEEB0265DC7 FOREIGN KEY (contractor_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DDF91AC92');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D72757D19');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66DDF91AC92 FOREIGN KEY (creditor_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D72757D19 FOREIGN KEY (debitor_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE phone DROP FOREIGN KEY FK_444F97DDC7EF6AE6');
        $this->addSql('ALTER TABLE phone ADD CONSTRAINT FK_444F97DDC7EF6AE6 FOREIGN KEY (sms_data_id) REFERENCES user_sms_data (id)');
    }
}
