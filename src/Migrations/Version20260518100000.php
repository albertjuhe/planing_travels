<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260518100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add distance column (meters) to gpx table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE gpx ADD distance INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE gpx DROP distance');
    }
}
