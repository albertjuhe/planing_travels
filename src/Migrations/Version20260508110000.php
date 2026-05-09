<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260508110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add time_start and time_end columns to location_visit_date for hourly itinerary';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location_visit_date ADD time_start TIME DEFAULT NULL, ADD time_end TIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location_visit_date DROP time_start, DROP time_end');
    }
}
