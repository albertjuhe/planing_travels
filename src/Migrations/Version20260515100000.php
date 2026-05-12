<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260515100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'F2: Add journal_entry and journal_photo tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE journal_entry (
            id VARCHAR(36) NOT NULL,
            travel_id VARCHAR(36) NOT NULL,
            author_id INT NOT NULL,
            entry_date DATE NOT NULL,
            title VARCHAR(255) DEFAULT NULL,
            content LONGTEXT NOT NULL,
            mood VARCHAR(20) DEFAULT NULL,
            weather_snapshot LONGTEXT DEFAULT NULL,
            is_public TINYINT(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            INDEX journal_travel_idx (travel_id),
            INDEX journal_date_idx (entry_date),
            INDEX journal_author_idx (author_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE journal_entry
            ADD CONSTRAINT FK_journal_entry_travel FOREIGN KEY (travel_id) REFERENCES travel (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_journal_entry_author FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE
        ');

        $this->addSql('CREATE TABLE journal_photo (
            id VARCHAR(36) NOT NULL,
            entry_id VARCHAR(36) NOT NULL,
            filename VARCHAR(255) NOT NULL,
            caption VARCHAR(500) DEFAULT NULL,
            taken_at DATETIME DEFAULT NULL,
            geo_lat DOUBLE PRECISION DEFAULT NULL,
            geo_lng DOUBLE PRECISION DEFAULT NULL,
            linked_location_id VARCHAR(36) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            INDEX journal_photo_entry_idx (entry_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE journal_photo
            ADD CONSTRAINT FK_journal_photo_entry FOREIGN KEY (entry_id) REFERENCES journal_entry (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE journal_photo DROP FOREIGN KEY FK_journal_photo_entry');
        $this->addSql('DROP TABLE journal_photo');
        $this->addSql('ALTER TABLE journal_entry DROP FOREIGN KEY FK_journal_entry_travel');
        $this->addSql('ALTER TABLE journal_entry DROP FOREIGN KEY FK_journal_entry_author');
        $this->addSql('DROP TABLE journal_entry');
    }
}
