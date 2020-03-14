<?php

namespace App\UI\Controller\API;

use App\Application\UseCases\User\GetUserTokenService;
use App\Domain\User\Exceptions\BadCredentialsException;
use App\Domain\User\Exceptions\UserDoesntExists;
use http\Client\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        $jsonData = json_decode($request->getContent(), true);
        if (!$jsonData) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid json data');
        }

        $username = $jsonData['username'];
        $password = $jsonData['password'];

        try {
            $token = $this->getUSerTokenService->execute($username, $password);
        } catch (UserDoesntExists | BadCredentialsException $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
        }

        return new JsonResponse(['token' => $token]);
    }
}
