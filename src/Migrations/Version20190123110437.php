<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190123110437 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE transaction_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, cyclosID BIGINT NOT NULL, title VARCHAR(20) DEFAULT NULL, reason LONGTEXT DEFAULT NULL, amount INT NOT NULL, date DATETIME NOT NULL, UNIQUE INDEX UNIQ_6D28840DF5EC4AE8 (cyclosID), INDEX IDX_6D28840D12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE access_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B6A2DD685F37A13B (token), INDEX IDX_B6A2DD6819EB6921 (client_id), INDEX IDX_B6A2DD68A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, random_id VARCHAR(255) NOT NULL, redirect_uris LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', secret VARCHAR(255) NOT NULL, allowed_grant_types LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cairn_user (id INT AUTO_INCREMENT NOT NULL, address_id INT DEFAULT NULL, image_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', name VARCHAR(255) DEFAULT NULL, cyclos_id BIGINT NOT NULL, description LONGTEXT NOT NULL, creation_date DATETIME NOT NULL, pwd_tries SMALLINT NOT NULL, card_key_tries SMALLINT NOT NULL, card_association_tries SMALLINT NOT NULL, removal_request TINYINT(1) NOT NULL, first_login TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_27B071592FC23A8 (username_canonical), UNIQUE INDEX UNIQ_27B0715A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_27B0715C05FB297 (confirmation_token), UNIQUE INDEX UNIQ_27B07155E237E06 (name), UNIQUE INDEX UNIQ_27B0715E7849E1 (cyclos_id), UNIQUE INDEX UNIQ_27B0715F5B7AF75 (address_id), UNIQUE INDEX UNIQ_27B07153DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY(user_source, user_target)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_token (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C74F21955F37A13B (token), INDEX IDX_C74F219519EB6921 (client_id), INDEX IDX_C74F2195A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operation (id INT AUTO_INCREMENT NOT NULL, creditor_id INT DEFAULT NULL, debitor_id INT DEFAULT NULL, type INT NOT NULL, paymentID VARCHAR(25) DEFAULT NULL, submissionDate DATETIME NOT NULL, executionDate DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, reason VARCHAR(255) NOT NULL, amount INT NOT NULL, fromAccountNumber VARCHAR(25) NOT NULL, toAccountNumber VARCHAR(25) NOT NULL, creditorName VARCHAR(50) NOT NULL, debitorName VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_1981A66D4EA83766 (paymentID), INDEX IDX_1981A66DDF91AC92 (creditor_id), INDEX IDX_1981A66D72757D19 (debitor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE zip_city (id INT AUTO_INCREMENT NOT NULL, zip_code VARCHAR(5) NOT NULL, city VARCHAR(180) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, fields TEXT NOT NULL, rows SMALLINT NOT NULL, cols SMALLINT NOT NULL, creation_date DATETIME DEFAULT NULL, salt VARCHAR(400) NOT NULL, code VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_161498D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banknote_status (id INT AUTO_INCREMENT NOT NULL, exchange_office_id INT NOT NULL, status VARCHAR(20) NOT NULL, INDEX IDX_89390406B2885B05 (exchange_office_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE beneficiary (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ICC VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7ABF446AB48B63B1 (ICC), INDEX IDX_7ABF446AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE beneficiary_user (beneficiary_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C902E85ECCAAFA0 (beneficiary_id), INDEX IDX_C902E85A76ED395 (user_id), PRIMARY KEY(beneficiary_id, user_id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auth_code (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, redirect_uri LONGTEXT NOT NULL, expires_at INT DEFAULT NULL, scope VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_5933D02C5F37A13B (token), INDEX IDX_5933D02C19EB6921 (client_id), INDEX IDX_5933D02CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE banknote (id INT AUTO_INCREMENT NOT NULL, status_id INT NOT NULL, number INT NOT NULL, value INT NOT NULL, lastUpdate DATETIME NOT NULL, INDEX IDX_E8C832806BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, zip_city_id INT NOT NULL, street1 VARCHAR(255) NOT NULL, street2 VARCHAR(255) DEFAULT NULL, INDEX IDX_D4E6F81309D0B4F (zip_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reconversion (id INT AUTO_INCREMENT NOT NULL, cyclosID BIGINT NOT NULL, submissionDate DATETIME NOT NULL, validationDate DATETIME DEFAULT NULL, user LONGTEXT NOT NULL COMMENT \'(DC2Type:object)\', amount NUMERIC(10, 2) NOT NULL, status VARCHAR(20) NOT NULL, UNIQUE INDEX UNIQ_CC3CAC4CF5EC4AE8 (cyclosID), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, alt VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE UTF8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D12469DE2 FOREIGN KEY (category_id) REFERENCES transaction_category (id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD6819EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B0715F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id)');
        $this->addSql('ALTER TABLE cairn_user ADD CONSTRAINT FK_27B07153DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES cairn_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES cairn_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F219519EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_C74F2195A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66DDF91AC92 FOREIGN KEY (creditor_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE operation ADD CONSTRAINT FK_1981A66D72757D19 FOREIGN KEY (debitor_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D3A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE banknote_status ADD CONSTRAINT FK_89390406B2885B05 FOREIGN KEY (exchange_office_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE beneficiary ADD CONSTRAINT FK_7ABF446AA76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE beneficiary_user ADD CONSTRAINT FK_C902E85ECCAAFA0 FOREIGN KEY (beneficiary_id) REFERENCES beneficiary (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE beneficiary_user ADD CONSTRAINT FK_C902E85A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02C19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE auth_code ADD CONSTRAINT FK_5933D02CA76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id)');
        $this->addSql('ALTER TABLE banknote ADD CONSTRAINT FK_E8C832806BF700BD FOREIGN KEY (status_id) REFERENCES banknote_status (id)');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81309D0B4F FOREIGN KEY (zip_city_id) REFERENCES zip_city (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D12469DE2');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD6819EB6921');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F219519EB6921');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02C19EB6921');
        $this->addSql('ALTER TABLE access_token DROP FOREIGN KEY FK_B6A2DD68A76ED395');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('ALTER TABLE refresh_token DROP FOREIGN KEY FK_C74F2195A76ED395');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66DDF91AC92');
        $this->addSql('ALTER TABLE operation DROP FOREIGN KEY FK_1981A66D72757D19');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D3A76ED395');
        $this->addSql('ALTER TABLE banknote_status DROP FOREIGN KEY FK_89390406B2885B05');
        $this->addSql('ALTER TABLE beneficiary DROP FOREIGN KEY FK_7ABF446AA76ED395');
        $this->addSql('ALTER TABLE beneficiary_user DROP FOREIGN KEY FK_C902E85A76ED395');
        $this->addSql('ALTER TABLE auth_code DROP FOREIGN KEY FK_5933D02CA76ED395');
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81309D0B4F');
        $this->addSql('ALTER TABLE banknote DROP FOREIGN KEY FK_E8C832806BF700BD');
        $this->addSql('ALTER TABLE beneficiary_user DROP FOREIGN KEY FK_C902E85ECCAAFA0');
        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B0715F5B7AF75');
        $this->addSql('ALTER TABLE cairn_user DROP FOREIGN KEY FK_27B07153DA5256D');
        $this->addSql('DROP TABLE transaction_category');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE cairn_user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP TABLE operation');
        $this->addSql('DROP TABLE zip_city');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE banknote_status');
        $this->addSql('DROP TABLE beneficiary');
        $this->addSql('DROP TABLE beneficiary_user');
        $this->addSql('DROP TABLE auth_code');
        $this->addSql('DROP TABLE banknote');
        $this->addSql('DROP TABLE address');
        $this->addSql('DROP TABLE reconversion');
        $this->addSql('DROP TABLE image');
    }
}
