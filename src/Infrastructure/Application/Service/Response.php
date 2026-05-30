<?php

namespace App\Infrastructure\Application\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class Response
{
    public static function json($success, $data, $message = ''): JsonResponse
    {
        $response = ['success' => $success];
        if ($success) {
            $response['data'] = $data;
        } else {
            $response['error'] = $data;
        }
        if (!empty($message)) {
            $response = ['message' => $message];
        }

        return new JsonResponse($response);
    }
}
