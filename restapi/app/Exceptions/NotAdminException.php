<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Log;

class NotAdminException extends Exception
{
    use ApiResponser;

    public function report() {

       //Log::debug("someone is accessing admin lists without being admin");
       
    }

    public function render($request) {
        
        return $this->errorResponse(["error" => "only admin is allowed to perfom this operation"], 422);
        
    }
}
