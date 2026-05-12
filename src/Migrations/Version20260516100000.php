<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260516100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'F1: Splitwise — add payer/splitMode/shares to travel_expense, create expense_share and settlement tables, backfill legacy expenses';
    }

    public function up(Schema $schema): void
    {
        // Alter travel_expense
        $this->addSql('ALTER TABLE travel_expense
            ADD payer_user_id INT DEFAULT NULL,
            ADD split_mode VARCHAR(10) NOT NULL DEFAULT \'equal\',
            ADD amount_in_travel_currency DECIMAL(10,2) NOT NULL DEFAULT 0,
            ADD exchange_rate_at_creation DOUBLE PRECISION NOT NULL DEFAULT 1
        ');

        $this->addSql('ALTER TABLE travel_expense
            ADD CONSTRAINT FK_expense_payer FOREIGN KEY (payer_user_id) REFERENCES users (id) ON DELETE SET NULL
        ');

        $this->addSql('CREATE INDEX expense_payer_idx ON travel_expense (payer_user_id)');

        // Backfill: set payer = travel owner for existing expenses, amount_in_travel_currency = amount
        $this->addSql('UPDATE travel_expense te
            JOIN travel t ON te.travel_id = t.id
            SET te.payer_user_id = t.user_id,
                te.amount_in_travel_currency = te.amount
            WHERE te.payer_user_id IS NULL
        ');

        // Create expense_share
        $this->addSql('CREATE TABLE expense_share (
            id INT AUTO_INCREMENT NOT NULL,
            expense_id INT NOT NULL,
            debtor_user_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            amount_in_travel_currency DECIMAL(10,2) NOT NULL,
            settled_at DATETIME DEFAULT NULL,
            INDEX share_expense_idx (expense_id),
            INDEX share_debtor_idx (debtor_user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE expense_share
            ADD CONSTRAINT FK_share_expense FOREIGN KEY (expense_id) REFERENCES travel_expense (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_share_debtor FOREIGN KEY (debtor_user_id) REFERENCES users (id) ON DELETE CASCADE
        ');

        // Backfill: create a single self-share for each legacy expense (payer owes nothing to themselves)
        $this->addSql('INSERT INTO expense_share (expense_id, debtor_user_id, amount, amount_in_travel_currency)
            SELECT te.id, te.payer_user_id, te.amount, te.amount_in_travel_currency
            FROM travel_expense te
            WHERE te.payer_user_id IS NOT NULL
        ');

        // Create settlement
        $this->addSql('CREATE TABLE settlement (
            id VARCHAR(36) NOT NULL,
            travel_id VARCHAR(36) NOT NULL,
            from_user_id INT NOT NULL,
            to_user_id INT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            currency VARCHAR(3) NOT NULL,
            settled_at DATETIME NOT NULL,
            note VARCHAR(500) DEFAULT NULL,
            INDEX settlement_travel_idx (travel_id),
            INDEX settlement_from_idx (from_user_id),
            INDEX settlement_to_idx (to_user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE settlement
            ADD CONSTRAINT FK_settlement_travel FOREIGN KEY (travel_id) REFERENCES travel (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_settlement_from FOREIGN KEY (from_user_id) REFERENCES users (id) ON DELETE CASCADE,
            ADD CONSTRAINT FK_settlement_to FOREIGN KEY (to_user_id) REFERENCES users (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE settlement DROP FOREIGN KEY FK_settlement_travel');
        $this->addSql('ALTER TABLE settlement DROP FOREIGN KEY FK_settlement_from');
        $this->addSql('ALTER TABLE settlement DROP FOREIGN KEY FK_settlement_to');
        $this->addSql('DROP TABLE settlement');

        $this->addSql('ALTER TABLE expense_share DROP FOREIGN KEY FK_share_expense');
        $this->addSql('ALTER TABLE expense_share DROP FOREIGN KEY FK_share_debtor');
        $this->addSql('DROP TABLE expense_share');

        $this->addSql('ALTER TABLE travel_expense DROP FOREIGN KEY FK_expense_payer');
        $this->addSql('DROP INDEX expense_payer_idx ON travel_expense');
        $this->addSql('ALTER TABLE travel_expense
            DROP payer_user_id,
            DROP split_mode,
            DROP amount_in_travel_currency,
            DROP exchange_rate_at_creation
        ');
    }
}
