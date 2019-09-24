<?php

namespace App\UI\Controller\API;

use App\Application\UseCases\User\GetUserTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TokenController extends AbstractController
{
    /**
     * @var GetUserTokenService
     */
    private $getUSerTokenService;

    public function __construct(GetUserTokenService $getUSerTokenService)
    {
        $this->getUSerTokenService = $getUSerTokenService;
    }

    /**
     * @Route("/api/tokens", name="get user token")
     * @Method({"POST"})
     */
    public function newTokenAction(Request $request): JsonResponse
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        $token = $this->getUSerTokenService->execute($username, $password);

        return new JsonResponse(['token' => $token]);
    }
}
