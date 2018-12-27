<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 27/12/18
 * Time: 10:44
 */

namespace App\Application\DataTransformers\Travel;

use App\Domain\Travel\Model\Travel;

interface TravelDataTransformer
{
    public function write(Travel $travel);
    /**
     * @return mixed
     */
    public function read();
}