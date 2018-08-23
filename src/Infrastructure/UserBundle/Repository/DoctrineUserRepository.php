<?php


namespace App\Infrastructure\UserBundle\Repository;

use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineUserRepository extends  ServiceEntityRepository implements UserRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user) {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }

}