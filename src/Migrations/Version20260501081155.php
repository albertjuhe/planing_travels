<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260501081155 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add missing indexes for performance';
    }

    public function up(Schema $schema) : void
    {
        // Index for location.travel_id
        $this->addSql('CREATE INDEX location_travel_id_idx ON location (travel_id)');
        $this->addSql('CREATE INDEX location_mark_id_idx ON location (mark_id)');
        $this->addSql('CREATE INDEX location_typelocation_id_idx ON location (typelocation_id)');
        $this->addSql('CREATE INDEX location_slug_idx ON location (slug)');

        // Index for images.location_id
        $this->addSql('CREATE INDEX images_location_id_idx ON images (location_id)');

        // Index for note.location_id
        $this->addSql('CREATE INDEX note_location_id_idx ON note (location_id)');

        // Index for gpx.travel_id
        $this->addSql('CREATE INDEX gpx_travel_id_idx ON gpx (travel_id)');

        // Index for travel.slug
        $this->addSql('CREATE INDEX travel_slug_idx ON travel (slug)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP INDEX location_travel_id_idx ON location');
        $this->addSql('DROP INDEX location_mark_id_idx ON location');
        $this->addSql('DROP INDEX location_typelocation_id_idx ON location');
        $this->addSql('DROP INDEX location_slug_idx ON location');
        $this->addSql('DROP INDEX images_location_id_idx ON images');
        $this->addSql('DROP INDEX note_location_id_idx ON note');
        $this->addSql('DROP INDEX gpx_travel_id_idx ON gpx');
        $this->addSql('DROP INDEX travel_slug_idx ON travel');
    }
}
