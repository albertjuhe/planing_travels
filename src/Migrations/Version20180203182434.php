<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180203182434 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE gpx (id INT AUTO_INCREMENT NOT NULL, travel_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, INDEX IDX_C338844FECAB15B3 (travel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, original VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, INDEX IDX_E01FBE6A64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, mark_id VARCHAR(150) DEFAULT NULL, travel_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, slug VARCHAR(128) NOT NULL, description LONGTEXT DEFAULT NULL, starts INT DEFAULT NULL, typeLocation_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_5E9E89CB989D9B62 (slug), INDEX IDX_5E9E89CBA76ED395 (user_id), INDEX IDX_5E9E89CB4290F12B (mark_id), INDEX IDX_5E9E89CBECAB15B3 (travel_id), INDEX IDX_5E9E89CBFE998804 (typeLocation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mark (id VARCHAR(150) NOT NULL, title VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, lat NUMERIC(14, 8) NOT NULL, lng NUMERIC(14, 8) NOT NULL, lat0 NUMERIC(14, 8) DEFAULT NULL, lng0 NUMERIC(14, 8) DEFAULT NULL, lat1 NUMERIC(14, 8) DEFAULT NULL, lng1 NUMERIC(14, 8) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nota (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_C8D03E0D64D218E (location_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE travel (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(128) NOT NULL, photo VARCHAR(255) DEFAULT NULL, lat NUMERIC(14, 8) NOT NULL, lng NUMERIC(14, 8) NOT NULL, lat0 NUMERIC(14, 8) DEFAULT NULL, lng0 NUMERIC(14, 8) DEFAULT NULL, lat1 NUMERIC(14, 8) DEFAULT NULL, lng1 NUMERIC(14, 8) DEFAULT NULL, startAt DATETIME NOT NULL, endAt DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, starts INT DEFAULT NULL, watch INT DEFAULT NULL, UNIQUE INDEX UNIQ_2D0B6BCE989D9B62 (slug), INDEX IDX_2D0B6BCEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE typelocation (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, icon VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, updatedAt DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gpx ADD CONSTRAINT FK_C338844FECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CB4290F12B FOREIGN KEY (mark_id) REFERENCES mark (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBECAB15B3 FOREIGN KEY (travel_id) REFERENCES travel (id)');
        $this->addSql('ALTER TABLE location ADD CONSTRAINT FK_5E9E89CBFE998804 FOREIGN KEY (typeLocation_id) REFERENCES typelocation (id)');
        $this->addSql('ALTER TABLE nota ADD CONSTRAINT FK_C8D03E0D64D218E FOREIGN KEY (location_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE travel ADD CONSTRAINT FK_2D0B6BCEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A64D218E');
        $this->addSql('ALTER TABLE nota DROP FOREIGN KEY FK_C8D03E0D64D218E');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CB4290F12B');
        $this->addSql('ALTER TABLE gpx DROP FOREIGN KEY FK_C338844FECAB15B3');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBECAB15B3');
        $this->addSql('ALTER TABLE location DROP FOREIGN KEY FK_5E9E89CBFE998804');
        $this->addSql('DROP TABLE gpx');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE mark');
        $this->addSql('DROP TABLE nota');
        $this->addSql('DROP TABLE travel');
        $this->addSql('DROP TABLE typelocation');
    }
}
