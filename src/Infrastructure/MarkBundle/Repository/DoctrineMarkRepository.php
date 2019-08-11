<?php

namespace App\Infrastructure\MarkBundle\Repository;

use App\Domain\Mark\Model\Mark;
use App\Domain\Mark\Repository\MarkRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class DoctrineMarkRepository extends ServiceEntityRepository implements MarkRepository
{
    /**
     * DoctrineTravelRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mark::class);
    }

    public function ofIdOrSave(Mark $mark): Mark
    {
        $markStore = $this->find($mark->getId());
        if (!$markStore instanceof Mark) {
            $this->save($mark);

            return $mark;
        }

        return $markStore;
    }

    public function save(Mark $mark): void
    {
        $this->_em->persist($mark);
    }
}
