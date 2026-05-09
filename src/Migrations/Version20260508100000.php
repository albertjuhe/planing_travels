<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260508100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add budget and expenses tables for travel budget tracking';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS travel_budget (
                id INT AUTO_INCREMENT PRIMARY KEY,
                travel_id CHAR(36) NOT NULL COMMENT \'(DC2Type:TravelId)\',
                amount DECIMAL(10,2) NOT NULL DEFAULT 0,
                currency VARCHAR(3) NOT NULL DEFAULT \'EUR\',
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                CONSTRAINT FK_BUDGET_TRAVEL FOREIGN KEY (travel_id) REFERENCES travel (id) ON DELETE CASCADE,
                CONSTRAINT UNIQ_BUDGET_TRAVEL UNIQUE (travel_id)
            ) COLLATE = utf8mb4_unicode_ci
        ');

        $this->addSql('
            CREATE TABLE IF NOT EXISTS travel_expense (
                id INT AUTO_INCREMENT PRIMARY KEY,
                travel_id CHAR(36) NOT NULL COMMENT \'(DC2Type:TravelId)\',
                location_id CHAR(36) NULL COMMENT \'(DC2Type:LocationId)\',
                description VARCHAR(255) NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                currency VARCHAR(3) NOT NULL DEFAULT \'EUR\',
                category VARCHAR(50) NOT NULL DEFAULT \'other\',
                expense_date DATE NULL,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                CONSTRAINT FK_EXPENSE_TRAVEL FOREIGN KEY (travel_id) REFERENCES travel (id) ON DELETE CASCADE,
                CONSTRAINT FK_EXPENSE_LOCATION FOREIGN KEY (location_id) REFERENCES location (id) ON DELETE SET NULL
            ) COLLATE = utf8mb4_unicode_ci
        ');

        $this->addSql('CREATE INDEX IDX_EXPENSE_TRAVEL ON travel_expense (travel_id)');
        $this->addSql('CREATE INDEX IDX_EXPENSE_LOCATION ON travel_expense (location_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS travel_expense');
        $this->addSql('DROP TABLE IF EXISTS travel_budget');
    }
}
