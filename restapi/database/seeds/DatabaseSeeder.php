<?php

use App\Buyer;
use App\Category;
use App\Product;
use App\Transaction;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        DB::statement("SET FOREIGN_KEY_CHECKS = 0");

        User::truncate();
        Product::truncate();
        Category::truncate();
        Buyer::truncate();
        Transaction::truncate();
        DB::table("category_product")->truncate();

        DB::statement("SET FOREIGN_KEY_CHECKS = 1");

        User::flushEventListeners();
        Product::flushEventListeners();
        Category::flushEventListeners();
        Buyer::flushEventListeners();
        Transaction::flushEventListeners();

        $nUser = 200;
        $nProduct = 1000;
        $nCategory = 30;
        $nTransaction = 1000;

        factory(User::class, $nUser)->create();
        factory(Category::class, $nCategory)->create();
        factory(Product::class, $nProduct)->create()->each(function($product) {
            $categories = Category::all()->random(mt_rand(1, 5))->pluck("id");
            $product->category()->attach($categories);
        });
        factory(Transaction::class, $nTransaction)->create();



    }
}
