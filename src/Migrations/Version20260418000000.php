<?php

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260418000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note MODIFY COLUMN title VARCHAR(255) NULL, MODIFY COLUMN description LONGTEXT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE note MODIFY COLUMN title VARCHAR(255) NOT NULL, MODIFY COLUMN description VARCHAR(255) NOT NULL');
    }
}
