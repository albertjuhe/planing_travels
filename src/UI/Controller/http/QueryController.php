<?php

namespace App\UI\Controller\http;

use App\Infrastructure\Application\QueryBus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QueryController extends AbstractController
{
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    public function ask($query)
    {
        return $this->queryBus->query($query);
    }
}
