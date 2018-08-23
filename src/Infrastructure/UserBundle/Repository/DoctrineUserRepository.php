<?php


namespace App\Infrastructure\UserBundle\Repository;

use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineUserRepository implements UserRepository
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManger)
    {
        $this->em = $entityManger;
    }

    public function save(User $user) {
        $this->em->persist($user);
        $this->em->flush($user);
    }

}