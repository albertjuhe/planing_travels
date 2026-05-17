<?php

namespace App\UI\Controller\API;

use App\Application\Query\Weather\GetTravelForecastQuery;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\User\Model\User;
use App\Infrastructure\Application\QueryBus\QueryBus;
use App\UI\Controller\http\QueryController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class GetTravelWeatherAPIController extends QueryController
{
    public function __construct(QueryBus $queryBus, Security $security)
    {
        parent::__construct($queryBus, $security);
    }

    #[Route('/api/travel/{slug}/weather', name: 'travel_weather', methods: ['GET'])]
    public function getWeather(string $slug): JsonResponse
    {
        /** @var User|null $currentUser */
        $currentUser = $this->security->getUser();
        if ($currentUser === null) {
            return new JsonResponse(['available' => false, 'reason' => 'forbidden'], 403);
        }

        $userId = $currentUser->getId()->id();

        try {
            $result = $this->ask(new GetTravelForecastQuery($slug, $userId));
        } catch (TravelDoesntExists $e) {
            return new JsonResponse(['available' => false, 'reason' => 'not_found'], 404);
        } catch (\Throwable $e) {
            return new JsonResponse(['available' => false, 'reason' => 'unavailable'], 200);
        }

        $response = new JsonResponse($result);
        $response->headers->set('Cache-Control', 'private, max-age=900');

        return $response;
    }
}
