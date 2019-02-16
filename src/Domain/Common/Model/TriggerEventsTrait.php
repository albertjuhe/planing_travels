<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 24/10/2018
 * Time: 08:00.
 */

namespace App\Domain\Common\Model;

trait TriggerEventsTrait
{
    private $events = [];

    protected function trigger($event)
    {
        $this->events[] = $event;
    }

    public function getEvents()
    {
        return $this->events;
    }
}
