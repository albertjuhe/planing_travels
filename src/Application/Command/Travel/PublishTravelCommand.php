<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 24/10/2018
 * Time: 19:05
 */

namespace App\Application\Command\Travel;

use App\Application\Command\Command;
use App\Domain\User\Model\User;

class PublishTravelCommand extends Command
{
    /** @var string */
    private $travelSlug;
    /** @var User */
    private $user;

    /**
     * PublishTravelCommand constructor.
     * @param string $travelSlug
     * @param User $user
     */
    public function __construct(string $travelSlug, User $user)
    {
        $this->travelSlug = $travelSlug;
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getTravelSlug(): string
    {
        return $this->travelSlug;
    }

    /**
     * @param string $travelSlug
     */
    public function setTravelSlug(string $travelSlug): void
    {
        $this->travelSlug = $travelSlug;
    }


    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}