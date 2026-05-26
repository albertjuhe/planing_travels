<?php

namespace App\UI\Controller\API;

use App\Domain\Itinerary\Exceptions\LocationSuggestionFailed;
use App\Domain\Itinerary\Repository\LocationSuggester;
use App\Domain\Travel\Exceptions\TravelDoesntExists;
use App\Domain\Travel\Repository\TravelRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SuggestLocationsAPIController extends AbstractController
{
    public function __construct(
        private readonly TravelRepository $travelRepository,
        private readonly LocationSuggester $suggester,
        private readonly Security $security,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/api/travel/{travelId}/suggest-locations', name: 'suggest_locations', methods: ['POST'])]
    public function suggest(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if ($user === null) {
            return new JsonResponse(['error' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $travel = $this->travelRepository->ofIdOrFail($travelId);
        } catch (TravelDoesntExists) {
            return new JsonResponse(['error' => 'Travel not found.'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Authorization: owner or shared user.
        $ownerId = (string) $travel->getUser()->getId()->id();
        $userId  = (string) $user->getId()->id();
        $allowed = $ownerId === $userId;
        if (!$allowed) {
            foreach ($travel->getSharedusers() as $sharedUser) {
                if ((string) $sharedUser->getId()->id() === $userId) {
                    $allowed = true;
                    break;
                }
            }
        }
        if (!$allowed) {
            return new JsonResponse(['error' => 'Operation not allowed.'], JsonResponse::HTTP_FORBIDDEN);
        }

        if ($travel->getStartAt() === null || $travel->getEndAt() === null) {
            return new JsonResponse(
                ['error' => 'This travel has no start or end date. Please set dates before requesting suggestions.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $numberOfDays = (int) floor(
            ($travel->getEndAt()->getTimestamp() - $travel->getStartAt()->getTimestamp()) / 86400
        ) + 1;
        $numberOfDays = max(1, $numberOfDays);

        $body        = json_decode($request->getContent(), true) ?? [];
        $preferences = isset($body['preferences']) && is_string($body['preferences'])
            ? mb_substr(trim($body['preferences']), 0, 500)
            : '';

        // Collect existing location titles to avoid duplicates.
        $existing = [];
        foreach ($travel->getLocation() as $loc) {
            if ($loc->getTitle()) {
                $existing[] = $loc->getTitle();
            }
        }

        $destination = $travel->getTitle() ?? '';
        $locale      = $request->getLocale() ?: 'en';

        try {
            $suggestions = $this->suggester->suggest(
                $destination,
                $numberOfDays,
                $locale,
                $preferences,
                $existing,
            );
        } catch (LocationSuggestionFailed $e) {
            $this->logger->warning('Location suggestion failed: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(
                ['error' => 'The AI assistant is currently unavailable: ' . $e->getMessage()],
                JsonResponse::HTTP_BAD_GATEWAY
            );
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error suggesting locations: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(
                ['error' => 'An unexpected error occurred: ' . $e->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse([
            'success'     => true,
            'suggestions' => array_map(static fn ($s) => $s->toArray(), $suggestions),
        ]);
    }
}
