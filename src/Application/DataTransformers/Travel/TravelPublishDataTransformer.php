<?php
/**
 * Created by PhpStorm.
 * User: ajuhe
 * Date: 27/12/18
 * Time: 10:45.
 */

namespace App\Application\DataTransformers\Travel;

use App\Domain\Travel\Model\Travel;

/**
 * DataTransformer for publish data events
 * Class TravelPublishDataTransformer.
 */
class TravelPublishDataTransformer implements TravelDataTransformer
{
    /** @var Travel */
    private $travel;

    public function __construct(Travel $travel)
    {
        $this->write($travel);
    }

    public function write(Travel $travel)
    {
        $this->travel = $travel;
    }

    public function read()
    {
        return [
           'id' => $this->travel->getId()->id(),
           'publishedAt' => $this->travel->getPublishedAt(),
           'status' => $this->travel->getStatus(),
       ];
    }
}
