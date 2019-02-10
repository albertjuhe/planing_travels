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
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mark::class);
    }

    public function ofIdOrSave($id): Mark
    {
        $mark = $this->find($id);
        if (!$mark instanceof Mark) $this->save($mark);

        return $mark;

    }


    public function save(Mark $mark)
    {
        $this->_em->persist($mark);
    }

}