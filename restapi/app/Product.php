<?php

namespace App;

use App\Seller;
use App\Category;
use App\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    
    use SoftDeletes;
    
    const AVAILABLE_PRODUCT = "available";
    const UNAVAILABLE_PRODUCT = "unavailable";

    protected $table = "products";

    protected $dates = [
        "deleted_at"
    ];
    protected $fillable = [
        "name",
        "description",
        "status",
        "image",
        "quantity",
        "seller_id"
    ];

    protected static function boot() {
        parent::boot();

        static::observe(new \App\Observer\ProductObserver);
    }
    
    public function isAvailable() {
        return $this->status == Product::AVAILABLE_PRODUCT;
    }

    public function seller() {
        return $this->belongsTo(Seller::class);
    }

    public function category() {
        return $this->belongsToMany(Category::class);
    }

    public function transaction() {
        return $this->hasMany(Transaction::class);
    }
    
    public function hasAttribute($attr) {
        return array_key_exists($attr, $this->attributes);
    }
}
