<?php

namespace App\UI\Controller\http;

use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Model\User;
use App\Infrastructure\Application\QueryBus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class QueryController extends AbstractController
{
    private $queryBus;
    protected $security;

    public function __construct(QueryBus $queryBus, Security $security)
    {
        $this->queryBus = $queryBus;
        $this->security = $security;
    }

    public function ask($query)
    {
        return $this->queryBus->query($query);
    }

    public function guard(): UserInterface
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new UserDoesntExists();
        }

        return $user;
    }
}
