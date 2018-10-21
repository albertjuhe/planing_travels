<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 12/10/2018
 * Time: 21:12
 */

namespace App\Application\Command\Travel;

use App\Domain\Travel\Model\Travel;

class AddTravelCommand
{
    /** @var Travel */
    private $travel;

    /**
     * UpdateTravelCommand constructor.
     * @param Travel $travel
     */
    public function __construct(Travel $travel)
    {
        $this->travel = $travel;
    }

    /**
     * @return Travel
     */
    public function getTravel(): Travel
    {
        return $this->travel;
    }

    /**
     * @param Travel $travel
     */
    public function setTravel(Travel $travel): void
    {
        $this->travel = $travel;
    }


}