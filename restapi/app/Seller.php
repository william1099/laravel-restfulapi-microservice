<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class Seller extends User
{
    public function product() {
        return $this->hasMany(Product::class);
    }
}
