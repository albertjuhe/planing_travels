<?php

namespace App\Application\Command\Location;

use App\Application\Command\Command;
use App\Domain\Location\Model\Location;
use App\Domain\Mark\Model\Mark;

class AddLocationCommand extends Command
{
    /**
     * @var int
     */
    private $travelId;

    /**
     * @var Location
     */
    private $location;

    /**
     * @var int
     */
    private $user;

    private $locationType;

    /**
     * @var Mark
     */
    private $mark;

    public function __construct(
        int $travelId,
        Location $location,
        int $user,
        Mark $mark,
        int $locationType
    ) {
        $this->travelId = $travelId;
        $this->location = $location;
        $this->user = $user;
        $this->mark = $mark;
        $this->locationType = $locationType;
    }

    /**
     * @return int
     */
    public function getTravelId(): int
    {
        return $this->travelId;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @return int
     */
    public function getUser(): int
    {
        return $this->user;
    }

    /**
     * @return Mark
     */
    public function getMark(): Mark
    {
        return $this->mark;
    }

    /**
     * @return int
     */
    public function getLocationType(): int
    {
        return $this->locationType;
    }
}
