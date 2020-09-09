<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer("quantity")->unsigned();
            $table->bigInteger("buyer_id")->unsigned();
            $table->bigInteger("product_id")->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign("buyer_id")->references("id")->on("users")->onDelete("CASCADE");
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
        Schema::table("transactions", function(Blueprint $table) {

            $table->dropForeign("transactions_buyer_id_foreign");
            $table->dropIndex("transactions_buyer_id_foreign");

            $table->dropForeign("transactions_product_id_foreign");
            $table->dropIndex("transactions_product_id_foreign");
        });
        
        Schema::dropIfExists('transactions');

    }
}
