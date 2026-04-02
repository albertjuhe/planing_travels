<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260402120000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            "Migration can only be executed safely on 'mysql'."
        );

        $this->addSql(
            'CREATE TABLE password_reset_tokens (
                id INT AUTO_INCREMENT NOT NULL,
                user_id INT NOT NULL,
                token_hash VARCHAR(64) NOT NULL,
                created_at DATETIME NOT NULL,
                expires_at DATETIME NOT NULL,
                used_at DATETIME DEFAULT NULL,
                UNIQUE INDEX UNIQ_E151F57A4FA70B0 (token_hash),
                INDEX IDX_E151F57A76ED395 (user_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql(
            'ALTER TABLE password_reset_tokens
             ADD CONSTRAINT FK_E151F57A76ED395 FOREIGN KEY (user_id)
             REFERENCES users (id) ON DELETE CASCADE'
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            "Migration can only be executed safely on 'mysql'."
        );

        $this->addSql('DROP TABLE password_reset_tokens');
    }
}
