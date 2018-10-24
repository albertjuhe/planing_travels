<?php
/**
 * Created by PhpStorm.
 * User: albert.juhe
 * Date: 24/10/2018
 * Time: 18:53
 */

namespace App\Infrastructure\TravelBundle\Notification;


use Doctrine\Common\EventSubscriber;

class TravelEventSubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return array(
            'add_travel_request_event',
            'publish_travel_request_event'
        );
    }

    /**
     * Event triggered after adding a Travel
     * @param TravelWasAdded $event
     */
    public function addTravelRequestEvent(TravelWasAdded $event) {

    }

    /**
     * Event triggered after publish a travel
     */
    public function publishTravelRequestEvent(RequestEvent $event) {

    }

}