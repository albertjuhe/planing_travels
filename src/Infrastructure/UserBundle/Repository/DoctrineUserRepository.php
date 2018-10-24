<?php


namespace App\Infrastructure\UserBundle\Repository;

use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use  App\Domain\User\Exceptions\UserSavingError;

class DoctrineUserRepository extends ServiceEntityRepository implements UserRepository
{
    /**
     * DoctrineUserRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function ofIdOrFail(int $userId): User {
        $user = $this->find($userId);
        if (null === $user) {
            throw new UserDoesntExists();
        }

        return $user;
    }

    /**
     * Get user by username
     * @param string $username
     * @return User|null
     */
    public function UserByUsername(string $username): ?User
    {
        return $this->findOneBy([
            'username' => $username
        ]);
    }

    /**
     * Save user in the database
     * @param User $user
     * @throws UserSavingError
     */
    public function save(User $user)
    {
        try {
            $this->_em->persist($user);
            $this->_em->flush($user);
        } catch (\Exception $e) {
            throw new UserSavingError('Error saving user: '.$e->getMessage());
        }
    }

}