<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 08/10/2018
 * Time: 07:36
 */

namespace App\Application\Command;

use App\Domain\Travel\Model\Travel;
use App\Domain\User\Model\User;

class UpdateTravelCommand
{
    /** @var Travel */
    private $travel;
    /** @var User */
    private $user;

    /**
     * UpdateTravelCommand constructor.
     * @param Travel $travel
     * @param User $user
     */
    public function __construct(Travel $travel, User $user)
    {
        $this->travel = $travel;
        $this->user = $user;
    }

    /**
     * @return Travel
     */
    public function travel(): Travel
    {
        return $this->travel;
    }

    /**
     * @return User
     */
    public function user(): User
    {
        return $this->user;
    }


}