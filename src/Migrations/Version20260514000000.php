<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260514000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create travel_clone table for lineage/tracing of cloned travels';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE travel_clone (
            id INT AUTO_INCREMENT NOT NULL,
            original_travel_id VARCHAR(36) NOT NULL,
            cloned_travel_id VARCHAR(36) NOT NULL,
            cloned_by_id INT NOT NULL,
            original_user_id INT NOT NULL,
            original_travel_title VARCHAR(255) NOT NULL,
            cloned_at DATETIME NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE travel_clone');
    }
}
