<?php

namespace App\Infrastructure\Application\Service;

class Response
{
    /**
     * Response in JSON format.
     *
     * @param $success
     * @param $data
     * @param $message
     */
    public static function json($success, $data, $message = '')
    {
        header('Content-Type: application/json');
        $response = ['success' => $success];
        if ($success) {
            $response['data'] = $data;
        } else {
            $response['error'] = $data;
        }
        if (!empty($message)) {
            $response = ['message' => $message];
        }

        echo json_encode($response);
    }
}
