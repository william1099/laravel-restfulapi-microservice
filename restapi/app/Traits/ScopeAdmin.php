<?php

namespace App\Traits;

use App\Exceptions\NotAdminException;

trait ScopeAdmin {

    protected function scopeAdmin($query) {
        return $query->where("admin", "true");
    }

    
}