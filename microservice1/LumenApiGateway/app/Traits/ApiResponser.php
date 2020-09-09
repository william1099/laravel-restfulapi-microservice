<?php

namespace App\Traits;

use Illuminate\Http\Response;



trait ApiResponser {

    public function successResponse($data, $statusCode = Response::HTTP_OK) {
        return response($data, $statusCode)->header("Content-Type", "application/json");
    }

    public function errorResponse($message, $statusCode) {
        return response()->json([
            'error' => $message,
            'statusCode' => $statusCode
        ], $statusCode);
    }

    public function errorMessage($message, $statusCode) {
        return response($message, $statusCode)->header("Content-Type", "application/json");
    }

}