<?php

namespace App\UI\Controller\API;

use App\Infrastructure\WebSocket\WebSocketNotifier;
use App\UI\Controller\http\CommandController;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Security;

class SendChatMessageController extends CommandController
{
    private $security;
    private $wsNotifier;

    public function __construct(CommandBus $commandBus, Security $security, WebSocketNotifier $wsNotifier)
    {
        parent::__construct($commandBus);
        $this->security = $security;
        $this->wsNotifier = $wsNotifier;
    }

    /**
     * @Route("/api/travel/{travelId}/chat", name="send_chat_message")
     * @Method({"POST"})
     */
    public function sendMessage(Request $request, string $travelId): JsonResponse
    {
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $content = trim($data['content'] ?? '');

        if ($content === '') {
            return new JsonResponse(['error' => 'Empty message'], 400);
        }

        if (mb_strlen($content) > 1000) {
            return new JsonResponse(['error' => 'Message too long (max 1000 chars)'], 400);
        }

        $this->wsNotifier->sendChatMessage(
            $travelId,
            (string) $user->getId()->id(),
            $user->getUsername(),
            $content
        );

        return new JsonResponse(['status' => 'ok']);
    }
}
