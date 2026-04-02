<?php

namespace App\Infrastructure\UserBundle\Repository;

use App\Domain\User\Model\PasswordResetToken;
use App\Domain\User\Model\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrinePasswordResetTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PasswordResetToken::class);
    }

    public function save(PasswordResetToken $token): void
    {
        $this->_em->persist($token);
        $this->_em->flush($token);
    }

    public function findValidTokenByPlainValue(string $plainToken): ?PasswordResetToken
    {
        $tokenHash = hash('sha256', $plainToken);

        /** @var PasswordResetToken|null $token */
        $token = $this->findOneBy([
            'tokenHash' => $tokenHash,
            'usedAt' => null,
        ]);

        if (!$token instanceof PasswordResetToken || !$token->canBeUsed()) {
            return null;
        }

        return $token;
    }

    public function invalidateActiveUserTokens(User $user): void
    {
        $qb = $this->createQueryBuilder('prt');

        $qb
            ->update()
            ->set('prt.usedAt', ':usedAt')
            ->where('prt.user = :user')
            ->andWhere('prt.usedAt IS NULL')
            ->setParameter('usedAt', new \DateTime())
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
