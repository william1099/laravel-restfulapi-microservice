<?php 

namespace App\Observer;

use App\Product;

class ProductObserver {
    public function updated($product) {

        if($product->quantity == 0 && $product->isAvailable()) {
            $product->status = Product::UNAVAILABLE_PRODUCT;
            $product->save();
        }

    }
}