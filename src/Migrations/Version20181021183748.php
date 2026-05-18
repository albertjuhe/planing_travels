<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181021183748 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !($this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform || $this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDBPlatform),
            'Migration can only be executed safely on MySQL.'
        );
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (1, 'House', 'fa fa-bed', '2015-08-07 17:57:18', '2015-08-07 17:57:18', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (2, 'Airport', 'fa fa-plane', '2015-08-07 17:57:48', '2015-08-07 17:57:48', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (3, 'Monument', 'fa fa-camera', '2015-08-07 17:58:08', '2015-08-07 17:58:08', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (4, 'City', 'fa fa-building', '2015-08-07 17:58:21', '2015-08-07 17:58:21', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (5, 'Lunch', 'fa fa-cutlery', '2015-08-07 17:59:29', '2015-08-07 17:59:29', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (6, 'Bicycle', 'fa fa-bicycle', '2015-08-07 17:59:46', '2015-08-07 17:59:46', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (7, 'Bus', 'fa fa-bus', '2015-08-07 18:00:07', '2015-08-07 18:00:07', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (8, 'Automobile', 'fa fa-automobile', '2015-08-07 18:00:27', '2015-08-07 18:00:27', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (9, 'Train', 'fa fa-train', '2015-08-07 18:00:47', '2015-08-07 18:00:47', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (10, 'Ship', 'fa fa-ship', '2015-08-07 18:00:59', '2015-08-07 18:00:59', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (11, 'Coffee', 'fa fa-coffee', '2015-08-07 18:01:14', '2015-08-07 18:01:14', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (12, 'Park', 'fa fa-tree', '2015-08-07 18:01:15', '2015-08-07 18:01:15', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (13, 'Hotel', 'fa fa-building-o', '2015-08-07 18:01:16', '2015-08-07 18:01:16', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (14, 'Beach', 'fa fa-umbrella', '2015-08-07 18:01:17', '2015-08-07 18:01:17', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (15, 'Museum', 'fa fa-university', '2015-08-07 18:01:18', '2015-08-07 18:01:18', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (16, 'Shop', 'fa fa-shopping-cart', '2015-08-07 18:01:19', '2015-08-07 18:01:19', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (17, 'Camping', 'fa fa-fire', '2015-08-07 18:01:20', '2015-08-07 18:01:20', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (18, 'Viewpoint', 'fa fa-binoculars', '2015-08-07 18:01:21', '2015-08-07 18:01:21', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (19, 'Hospital', 'fa fa-hospital-o', '2015-08-07 18:01:22', '2015-08-07 18:01:22', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (20, 'Cinema', 'fa fa-film', '2015-08-07 18:01:23', '2015-08-07 18:01:23', null)");
        $this->addSql("INSERT INTO typelocation (id, title, icon, created_at, updated_at, description) VALUES (21, 'Bar', 'fa fa-glass', '2015-08-07 18:01:24', '2015-08-07 18:01:24', null)");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
