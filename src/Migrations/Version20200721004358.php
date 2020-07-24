<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200721004358 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_pro_category (user_id INT NOT NULL, pro_category_id INT NOT NULL, INDEX IDX_9774C759A76ED395 (user_id), INDEX IDX_9774C759C42348BB (pro_category_id), PRIMARY KEY(user_id, pro_category_id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pro_category (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_17B8BB5B989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_pro_category ADD CONSTRAINT FK_9774C759A76ED395 FOREIGN KEY (user_id) REFERENCES cairn_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_pro_category ADD CONSTRAINT FK_9774C759C42348BB FOREIGN KEY (pro_category_id) REFERENCES pro_category (id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO pro_category (slug,name) VALUES ("artisanat","Artisanat"),("association","Association"),("batiment-habitat","Bâtiment, habitat"),("commerces-alimentaires","Commerces alimentaires"),("comptoir-de-change","Comptoir de change"),("culture-loisirs","Culture, Loisirs"),("entretien-nettoyage","Entretien, nettoyage"),("espaces-verts","Espaces Verts"),("etudes-conseils-formations","Études, conseils, formations"),("fournitures-et-materiel","Fournitures et matériel"),("graphisme-informatique-web","Graphisme, informatique, web"),("habillement-mode-accessoires","Habillement, mode, accessoires"),("hebergement","Hébergement"),("hygiene-beaute","Hygiène, beauté"),("immobilier-materiaux-de-construction","Immobilier, Matériaux de construction"),("institutions","Institutions"),("livraison","Livraison"),("marche","Marché"),("mecanique-reparation","Mécanique, réparation"),("produits-de-lagriculture-et-de-lelevage","Produits de l\'agriculture et de l\'élevage"),("restauration-bars-traiteurs","Restaurant, bars, traiteurs"),("sante-bien-etre","Santé, bien être"),("sorties-culturelles","Sorties culturelles"),("transport-demenagement","Transport, déménagement")');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_pro_category DROP FOREIGN KEY FK_9774C759C42348BB');
        $this->addSql('DROP TABLE user_pro_category');
        $this->addSql('DROP TABLE pro_category');
    }
}
