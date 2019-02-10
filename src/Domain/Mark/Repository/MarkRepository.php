<?php


namespace App\Domain\Mark\Repository;


use App\Domain\Mark\Model\Mark;

interface MarkRepository
{
     /**
     * @param $id
     * @return Mark|null
     */
    public function ofIdOrSave(Mark $mark): ?Mark;

    /**
     * @param Mark $mark
     * @return mixed
     */
    public function save(Mark $mark);

}