<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260518175927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add 17 new TypeLocation entries';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (23, 'Cabin', 'fa fa-house-chimney', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (24, 'Lighthouse', 'fa fa-tower-observation', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (25, 'Bridge', 'fa fa-bridge', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (26, 'Stadium', 'fa fa-futbol', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (27, 'Zoo', 'fa fa-paw', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (28, 'Garden', 'fa fa-seedling', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (29, 'Theatre', 'fa fa-masks-theater', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (30, 'Farm', 'fa fa-tractor', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (31, 'Castle', 'fa fa-chess-rook', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (32, 'Cave', 'fa fa-mountain', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (33, 'Market', 'fa fa-store', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (34, 'Harbour', 'fa fa-anchor', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (35, 'Swimming', 'fa fa-person-swimming', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (36, 'Skiing', 'fa fa-person-skiing', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (37, 'Library', 'fa fa-book', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (38, 'Pharmacy', 'fa fa-prescription-bottle', NOW(), NOW(), null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (39, 'Bakery', 'fa fa-bread-slice', NOW(), NOW(), null)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM typelocation WHERE id IN (23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39)");
    }
}
