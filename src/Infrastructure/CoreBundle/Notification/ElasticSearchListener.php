<?php


namespace App\Infrastructure\CoreBundle\Notification;

use Doctrine\ORM\Event\LifecycleEventArgs;
use App\Domain\Travel;
use App\Infrastructure\CoreBundle\Repository\ElasticSearchRepository;

class ElasticSearchListener
{

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof Travel) {
            // ... do something with the Product
            /*
        $this->elasticSearchTravelRepository->save($entity);
*/
        }
    }
}