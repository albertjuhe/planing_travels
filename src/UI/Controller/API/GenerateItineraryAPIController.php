<?php

namespace App\UI\Controller\API;

use App\Application\Command\Travel\GenerateItineraryCommand;
use App\Domain\Itinerary\Exceptions\ItineraryOptimizationFailed;
use App\Domain\Itinerary\Exceptions\TravelHasNoDates;
use App\Domain\Travel\Exceptions\InvalidTravelUser;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\UI\Controller\http\CommandController;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;

class GenerateItineraryAPIController extends CommandController
{
    private const ALLOWED_MODES = ['all', 'unscheduled'];

    public function __construct(
        MessageBusInterface $commandBus,
        private readonly Security $security,
        private readonly LoggerInterface $logger,
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

        try {
            $limiter = $this->itineraryGenerationLimiter->create($travelId);
            $limit = $limiter->consume(1);
            if (!$limit->isAccepted()) {
                return new JsonResponse(
                    ['error' => 'Too many requests. Please wait a few minutes before generating again.'],
                    JsonResponse::HTTP_TOO_MANY_REQUESTS
                );
            }
        } catch (\Throwable $e) {
            $this->logger->error('Rate limiter unavailable for itinerary generation: ' . $e->getMessage(), ['exception' => $e]);
            // Fail open: don't block the user because of a misconfigured limiter.
        }

        $body = json_decode($request->getContent(), true) ?? [];
        $mode = isset($body['mode']) && in_array($body['mode'], self::ALLOWED_MODES, true)
            ? $body['mode']
            : 'all';
        $additionalNotes = isset($body['notes']) && is_string($body['notes'])
            ? mb_substr(trim($body['notes']), 0, 1000)
            : '';

        $locale = $request->getLocale() ?: 'en';

        $command = new GenerateItineraryCommand(
            travelId: $travelId,
            userId: $user->getId()->id(),
            mode: $mode,
            locale: $locale,
            additionalNotes: $additionalNotes,
        );

        try {
            $envelope = $this->commandBus->dispatch($command);

            $summary = ['daysAffected' => 0, 'locationsAssigned' => 0];
            $handled = $envelope->last(HandledStamp::class);
            if ($handled !== null) {
                $result = $handled->getResult();
                if (is_array($result)) {
                    $summary = $result;
                }
            }

            return new JsonResponse(['success' => true, 'summary' => $summary]);
        } catch (HandlerFailedException $e) {
            $cause = $this->extractRootCause($e);
            return $this->mapDomainExceptionToResponse($cause);
        } catch (TravelDoesntExists | InvalidTravelUser | TravelHasNoDates | ItineraryOptimizationFailed $e) {
            return $this->mapDomainExceptionToResponse($e);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error generating itinerary: ' . $e->getMessage(), [
                'exception' => $e,
                'travelId' => $travelId,
            ]);
            return new JsonResponse(
                ['error' => 'An unexpected error occurred: ' . $e->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function extractRootCause(HandlerFailedException $e): \Throwable
    {
        if (method_exists($e, 'getWrappedExceptions')) {
            $wrapped = $e->getWrappedExceptions();
            if (!empty($wrapped)) {
                return reset($wrapped);
            }
        }
        return $e->getPrevious() ?? $e;
    }

    private function mapDomainExceptionToResponse(\Throwable $cause): JsonResponse
    {
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
            $this->logger->warning('Itinerary optimization failed: ' . $cause->getMessage(), ['exception' => $cause]);
            return new JsonResponse(
                ['error' => 'The AI assistant is currently unavailable: ' . $cause->getMessage()],
                JsonResponse::HTTP_BAD_GATEWAY
            );
        }

        $this->logger->error('Unhandled exception generating itinerary: ' . $cause->getMessage(), ['exception' => $cause]);
        return new JsonResponse(
            ['error' => 'An unexpected error occurred: ' . $cause->getMessage()],
            JsonResponse::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
