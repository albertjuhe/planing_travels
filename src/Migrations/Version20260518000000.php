<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260518000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add visit_date column to gpx table for calendar assignment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE gpx ADD visit_date DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE gpx DROP visit_date');
    }
}
