<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260513120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'F4: Add travel clone tracking (clone fields on travel, travel_clone table, cloned_reference on images)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE travel
            ADD cloned_from_travel_id VARCHAR(36) DEFAULT NULL,
            ADD cloned_from_user_id INT DEFAULT NULL,
            ADD cloned_from_title VARCHAR(255) DEFAULT NULL,
            ADD cloned_at DATETIME DEFAULT NULL,
            ADD clone_count INT NOT NULL DEFAULT 0
        ');

        $this->addSql('CREATE INDEX travel_cloned_from_idx ON travel (cloned_from_travel_id)');

        $this->addSql('CREATE TABLE travel_clone (
            id VARCHAR(36) NOT NULL,
            source_travel_id VARCHAR(36) NOT NULL,
            source_user_id INT NOT NULL,
            source_title_snapshot VARCHAR(255) NOT NULL,
            target_travel_id VARCHAR(36) NOT NULL,
            cloned_by_user_id INT NOT NULL,
            cloned_at DATETIME NOT NULL,
            depth INT NOT NULL DEFAULT 1,
            UNIQUE INDEX UNIQ_TRAVEL_CLONE_TARGET (target_travel_id),
            INDEX travel_clone_source_idx (source_travel_id),
            INDEX travel_clone_by_user_idx (cloned_by_user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE travel_clone
            ADD CONSTRAINT FK_travel_clone_target FOREIGN KEY (target_travel_id) REFERENCES travel (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_travel_clone_user FOREIGN KEY (cloned_by_user_id) REFERENCES users (id) ON DELETE CASCADE
        ');

        $this->addSql('ALTER TABLE images
            ADD is_cloned_reference TINYINT(1) NOT NULL DEFAULT 0,
            ADD original_image_id INT DEFAULT NULL
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE travel_clone DROP FOREIGN KEY FK_travel_clone_target');
        $this->addSql('ALTER TABLE travel_clone DROP FOREIGN KEY FK_travel_clone_user');
        $this->addSql('DROP TABLE travel_clone');

        $this->addSql('DROP INDEX travel_cloned_from_idx ON travel');
        $this->addSql('ALTER TABLE travel
            DROP cloned_from_travel_id,
            DROP cloned_from_user_id,
            DROP cloned_from_title,
            DROP cloned_at,
            DROP clone_count
        ');

        $this->addSql('ALTER TABLE images
            DROP is_cloned_reference,
            DROP original_image_id
        ');
    }
}
