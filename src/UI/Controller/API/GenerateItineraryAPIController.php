<?php

namespace App\UI\Controller\API;

use App\Application\Command\Travel\GenerateItineraryCommand;
use App\Domain\Itinerary\Exceptions\ItineraryOptimizationFailed;
use App\Domain\Itinerary\Exceptions\TravelHasNoDates;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\UI\Controller\http\CommandController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

class GenerateItineraryAPIController extends CommandController
{
    private const ALLOWED_MODES = ['all', 'unscheduled'];

    public function __construct(
        MessageBusInterface $commandBus,
        private readonly Security $security,
        private readonly RateLimiterFactory $itineraryGenerationLimiter,
    ) {
        parent::__construct($commandBus);
    }

    #[Route('/api/travel/{travelId}/generate-itinerary', name: 'generate_itinerary', methods: ['POST'])]
    public function generate(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if ($user === null) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $limiter = $this->itineraryGenerationLimiter->create($travelId);
        $limit = $limiter->consume(1);
        if (!$limit->isAccepted()) {
            return new JsonResponse(
                ['error' => 'Too many requests. Please wait a few minutes before generating again.'],
                JsonResponse::HTTP_TOO_MANY_REQUESTS
            );
        }

        $body = json_decode($request->getContent(), true) ?? [];
        $mode = isset($body['mode']) && in_array($body['mode'], self::ALLOWED_MODES, true)
            ? $body['mode']
            : 'all';

        $locale = $request->getLocale() ?: 'en';

        $command = new GenerateItineraryCommand(
            travelId: $travelId,
            userId: $user->getId()->id(),
            mode: $mode,
            locale: $locale,
        );

        try {
            $envelope = $this->commandBus->dispatch($command);

            $summary = ['daysAffected' => 0, 'locationsAssigned' => 0];
            foreach ($envelope->all(\Symfony\Component\Messenger\Stamp\HandledStamp::class) as $stamp) {
                $result = $stamp->getResult();
                if (is_array($result)) {
                    $summary = $result;
                }
            }

            return new JsonResponse(['success' => true, 'summary' => $summary]);
        } catch (HandlerFailedException $e) {
            $cause = $e->getPrevious() ?? $e;

            if ($cause instanceof TravelDoesntExists) {
                return new JsonResponse(['error' => 'Travel not found.'], JsonResponse::HTTP_NOT_FOUND);
            }
            if ($cause instanceof InvalidTravelUser) {
                return new JsonResponse(['error' => 'Operation not allowed.'], JsonResponse::HTTP_FORBIDDEN);
            }
            if ($cause instanceof TravelHasNoDates) {
                return new JsonResponse(
                    ['error' => 'This travel has no start or end date. Please set the travel dates before generating an itinerary.'],
                    JsonResponse::HTTP_UNPROCESSABLE_ENTITY
                );
            }
            if ($cause instanceof ItineraryOptimizationFailed) {
                return new JsonResponse(
                    ['error' => 'The AI assistant is currently unavailable. Please try again in a moment.'],
                    JsonResponse::HTTP_BAD_GATEWAY
                );
            }

            return new JsonResponse(['error' => 'An unexpected error occurred.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
