<?php


namespace App\Helpers;

class CustomResponse
{

    public static function success($message = "", $data = [], $status = 200){
        return self::generateResponse([
            'message' => $message,
            'data' => $data
        ], $status);
    }

    public static function error($message = "", $data = [], $status = 400){
        return self::generateResponse([
            'message' => $message,
            'errors' => $data
        ], $status);
    }

    private static function generateResponse($array, $status){
        return Response($array)->setStatusCode($status);
    }
}