<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $table = "categories";

    protected $dates = [
        "deleted_at"
    ];

    protected $fillable = [
        "name",
        "description"
    ];

    public function product() {
        return $this->belongsToMany(Product::class);
    }
}
