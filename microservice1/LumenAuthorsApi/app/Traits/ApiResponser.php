<?php

namespace App\Traits;

use Illuminate\Http\Response;



trait ApiResponser {

    public function successResponse($data, $statusCode = Response::HTTP_OK) {
        return response()->json(['data' => $data], $statusCode);
    }

    public function errorResponse($message, $statusCode) {
        return response()->json([
            'error' => $message,
            'statusCode' => $statusCode
        ], $statusCode);
    }

}