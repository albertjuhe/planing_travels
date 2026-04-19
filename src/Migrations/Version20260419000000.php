<?php

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260419000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE location_visit_date (
            id INT AUTO_INCREMENT NOT NULL,
            location_id CHAR(36) NOT NULL COMMENT \'(DC2Type:LocationId)\',
            visit_date DATE NOT NULL,
            INDEX IDX_LVD_LOCATION (location_id),
            UNIQUE INDEX UNIQ_LVD_LOC_DATE (location_id, visit_date),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE location_visit_date
            ADD CONSTRAINT FK_LVD_LOCATION FOREIGN KEY (location_id)
            REFERENCES location (id) ON DELETE CASCADE');

        $this->addSql('INSERT INTO location_visit_date (location_id, visit_date)
            SELECT id, visit_at FROM location WHERE visit_at IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE location_visit_date');
    }
}
