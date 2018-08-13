<?php


namespace App\Infrastructure\UserBundle\Repository;

use App\Domain\User\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use App\Domain\User\Model\User;

class DoctrineUserRepository extends EntityManager implements UserRepository
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * DoctrineUserRepository constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function save(User $user) {
        $this->em->persist($user);
        $this->em->flush($user);
    }

}