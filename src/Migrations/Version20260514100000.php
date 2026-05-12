<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260514100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'F3.2: Add exchange_rates table for currency conversion cache';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE exchange_rates (
            id INT AUTO_INCREMENT NOT NULL,
            from_currency VARCHAR(3) NOT NULL,
            to_currency VARCHAR(3) NOT NULL,
            rate DOUBLE PRECISION NOT NULL,
            fetched_at DATETIME NOT NULL,
            valid_for_date DATE NOT NULL,
            UNIQUE INDEX uq_exchange_rate (from_currency, to_currency, valid_for_date),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE exchange_rates');
    }
}
