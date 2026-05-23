<?php

namespace App\UI\Controller\API;

use App\Infrastructure\AIBundle\Provider\ItineraryProvider;
use App\Infrastructure\TravelBundle\Repository\DoctrineTravelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

class SuggestItineraryAPIController extends AbstractController
{
    public function __construct(
        private DoctrineTravelRepository $travelRepository,
        private ItineraryProvider $itineraryProvider,
        private Security $security,
    ) {
    }

    #[Route('/api/travel/{slug}/suggest-itinerary', name: 'suggest_itinerary', methods: ['POST'])]
    public function suggest(Request $request, string $slug): JsonResponse
    {
        $user = $this->security->getUser();
        
        try {
            $travel = $this->travelRepository->ofSlugOrFail($slug);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Travel not found'], 404);
        }

        $isOwner = false;
        $isShared = false;
        
        if ($user) {
            $travelOwner = $travel->getUser();
            $isOwner = $travelOwner->getId()->id() === $user->getId()->id();
            
            foreach ($travel->getSharedusers() as $sharedUser) {
                if ($sharedUser->getId()->id() === $user->getId()->id()) {
                    $isShared = true;
                    break;
                }
            }
        }

        if (!$isOwner && !$isShared) {
            return new JsonResponse(['error' => 'Operation not allowed'], 403);
        }

        $preferences = [];
        $content = $request->getContent();
        if ($content) {
            $data = json_decode($content, true);
            if (is_array($data)) {
                $preferences = $data;
            }
        }

        $locations = $travel->getLocation();
        $locationsArray = [];
        
        foreach ($locations as $loc) {
            $locationsArray[] = $loc;
        }

        try {
            $result = $this->itineraryProvider->generateItinerary(
                $travel->getTitle(),
                $travel->getStartAt(),
                $travel->getEndAt(),
                $locationsArray,
                $preferences
            );

            $result['backend'] = $this->itineraryProvider->getBackend();
            $result['model'] = $this->itineraryProvider->getModel();

            return new JsonResponse($result);

        } catch (\RuntimeException $e) {
            $backend = $this->itineraryProvider->getBackend();
            $hint = $backend === ItineraryProvider::BACKEND_OLLAMA
                ? 'Make sure Ollama is running: `ollama serve` and model is pulled: `ollama pull ' . $this->itineraryProvider->getModel() . '`'
                : 'Make sure OPENAI_API_KEY is configured in .env';

            return new JsonResponse([
                'error' => 'Itinerary generation failed',
                'backend' => $backend,
                'message' => $e->getMessage(),
                'hint' => $hint
            ], 500);
        }
    }
}
