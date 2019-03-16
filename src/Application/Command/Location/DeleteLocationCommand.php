<?php

namespace App\Application\Command\Location;

use App\Application\Command\Command;

class DeleteLocationCommand extends Command
{
    /**
     * @var int
     */
    private $locationId;

    /**
     * DeleteLocationCommand constructor.
     *
     * @param int $locationId
     */
    public function __construct(int $locationId)
    {
        $this->locationId = $locationId;
    }

    /**
     * @return int
     */
    public function getLocationId(): int
    {
        return $this->locationId;
    }
}
