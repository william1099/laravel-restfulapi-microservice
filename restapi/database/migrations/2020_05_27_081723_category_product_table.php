<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CategoryProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("category_product", function(Blueprint $table) {
            $table->bigInteger("category_id")->unsigned();
            $table->bigInteger("product_id")->unsigned();

            $table->foreign("category_id")->references("id")->on("categories")->onDelete("CASCADE");
            $table->foreign("product_id")->references("id")->on("products")->onDelete("CASCADE");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("category_product", function(Blueprint $table) {

            $table->dropForeign("category_product_category_id_foreign");
            $table->dropIndex("category_product_category_id_foreign");

            $table->dropForeign("category_product_product_id_foreign");
            $table->dropIndex("category_product_product_id_foreign");
        });

        
        Schema::dropIfExists("category_product");
        
    }
}
