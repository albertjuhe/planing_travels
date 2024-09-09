<?php

namespace App\UI\Controller\API;

use App\Application\Query\Travel\GetMyTravelsQuery;
use App\Domain\User\Exceptions\UserDoesntExists;
use App\Domain\User\Repository\UserRepository;
use App\Domain\User\ValueObject\UserId;
use App\Infrastructure\Application\QueryBus\QueryBus;
use App\Infrastructure\UserBundle\Repository\DoctrineUserRepository;
use App\UI\Controller\http\QueryController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GetUserTravels extends QueryController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        DoctrineUserRepository $userRepository,
        QueryBus $queryBus,
        Security $security
    ) {
        parent::__construct($queryBus, $security);
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("api/user/{userId}/travels", name="list travels by user")
     * @Method({"GET"})
     */
    public function getTravelsByUser(Request $request, int $userId): JsonResponse
    {
        try {
            $user = $this->userRepository->ofIdOrFail(new UserId($userId));
        } catch (UserDoesntExists $e) {
            return new JsonResponse(
                $response['error'] = 'Operation not allowed'
            );
        }

        $getMyTravelQuery = new GetMyTravelsQuery($user);
        $travels = $this->ask($getMyTravelQuery);

        $data = [
            'data' => $travels,
        ];
        $response = new JsonResponse(
            $data
        );

        return $response;
    }
}
