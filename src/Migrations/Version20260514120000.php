<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260514120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'F3.4: Add weather_forecast table for Open-Meteo cache';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE weather_forecast (
            id INT AUTO_INCREMENT NOT NULL,
            lat DOUBLE PRECISION NOT NULL,
            lng DOUBLE PRECISION NOT NULL,
            forecast_date DATE NOT NULL,
            temp_min DOUBLE PRECISION DEFAULT NULL,
            temp_max DOUBLE PRECISION DEFAULT NULL,
            weather_code INT DEFAULT NULL,
            icon VARCHAR(10) DEFAULT NULL,
            description VARCHAR(100) DEFAULT NULL,
            fetched_at DATETIME NOT NULL,
            is_historical TINYINT(1) NOT NULL DEFAULT 0,
            UNIQUE INDEX uq_weather_forecast (lat, lng, forecast_date),
            INDEX weather_date_idx (forecast_date),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE weather_forecast');
    }
}
