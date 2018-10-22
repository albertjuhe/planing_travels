<?php declare(strict_types=1);

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
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
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

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
