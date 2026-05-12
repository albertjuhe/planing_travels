<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260514110000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'F3.3: Add timezone column to location table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location ADD timezone VARCHAR(64) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE location DROP timezone');
    }
}
