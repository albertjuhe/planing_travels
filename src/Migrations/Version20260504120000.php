<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504120000 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add position column to location_visit_date table to track order of locations per day';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE location_visit_date ADD position INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_location_visit_date_position ON location_visit_date (position)');
        
        // Initialize positions for existing data
        $this->addSql('SET @row_number = 0');
        $this->addSql('SET @current_date = NULL');
        $this->addSql('
            UPDATE location_visit_date
            SET position = (@row_number := IF(@current_date = visit_date, @row_number + 1, 0))
            WHERE position IS NULL
            ORDER BY visit_date, id
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP INDEX IDX_location_visit_date_position ON location_visit_date');
        $this->addSql('ALTER TABLE location_visit_date DROP position');
    }
}
