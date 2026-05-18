<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260518173218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new TypeLocation entries: Park, Hotel, Beach, Museum, Shop, Camping, Viewpoint, Hospital, Cinema, Bar';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (12, 'Park', 'fa fa-tree', '2015-08-07 18:01:15', '2015-08-07 18:01:15', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (13, 'Hotel', 'fa fa-building', '2015-08-07 18:01:16', '2015-08-07 18:01:16', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (14, 'Beach', 'fa fa-sun', '2015-08-07 18:01:17', '2015-08-07 18:01:17', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (15, 'Museum', 'fa fa-university', '2015-08-07 18:01:18', '2015-08-07 18:01:18', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (16, 'Shop', 'fa fa-shopping-cart', '2015-08-07 18:01:19', '2015-08-07 18:01:19', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (17, 'Camping', 'fa fa-fire', '2015-08-07 18:01:20', '2015-08-07 18:01:20', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (18, 'Viewpoint', 'fa fa-binoculars', '2015-08-07 18:01:21', '2015-08-07 18:01:21', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (19, 'Hospital', 'fa fa-hospital', '2015-08-07 18:01:22', '2015-08-07 18:01:22', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (20, 'Cinema', 'fa fa-film', '2015-08-07 18:01:23', '2015-08-07 18:01:23', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (21, 'Bar', 'fa fa-glass-martini', '2015-08-07 18:01:24', '2015-08-07 18:01:24', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (22, 'Ruins', 'fa fa-landmark', '2015-08-07 18:01:25', '2015-08-07 18:01:25', null)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM typelocation WHERE id IN (12,13,14,15,16,17,18,19,20,21,22)");
    }
}
