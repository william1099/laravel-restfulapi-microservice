<?php

use App\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->text("description");
            $table->integer("quantity")->unsigned();
            $table->string("status")->default(Product::UNAVAILABLE_PRODUCT);
            $table->string("image");
            $table->bigInteger("seller_id")->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("seller_id")->references("id")->on("users")->onDelete("CASCADE");
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("products", function(Blueprint $table) {

            $table->dropForeign("products_seller_id_foreign");
            $table->dropIndex("products_seller_id_foreign");

        });

        Schema::dropIfExists('products');
    }
}
