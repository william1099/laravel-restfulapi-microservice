<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get("buyers/{id}/categories", "Buyer\BuyerController@buyerIndex");
Route::get("buyers/{id}/sellers", "Buyer\BuyerController@buyerIndex");
Route::get("buyers/{id}/products", "Buyer\BuyerController@buyerIndex");
Route::get("buyers/{id}/transactions", "Buyer\BuyerController@buyerIndex");
Route::post("buyers/{idBuyer}/products/{idProduct}/transactions", "Transaction\TransactionController@store");
Route::resource("buyers", "Buyer\BuyerController", ["only" => ["index", "show"]]);

Route::get("sellers/{id}/transactions", "Seller\SellerController@sellerIndex");
Route::get("sellers/{id}/products", "Seller\SellerController@sellerIndex");
Route::post("sellers/{id}/products", "Seller\SellerController@storeProduct");
Route::put("sellers/{idSeller}/products/{idProduct}", "Seller\SellerController@updateProduct");
Route::delete("sellers/{idSeller}/products/{idProduct}", "Seller\SellerController@deleteProduct");
Route::resource("sellers", "Seller\SellerController", ["only" => ["index", "show"]]);

Route::get("categories/{id}/products", "Category\CategoryController@categoryIndex");
Route::get("categories/{id}/buyers", "Category\CategoryController@categoryIndex");
Route::get("categories/{id}/sellers", "Category\CategoryController@categoryIndex");
Route::get("categories/{id}/transactions", "Category\CategoryController@categoryIndex");
Route::resource("categories", "Category\CategoryController", ["except" => ["create", "edit"]]);

Route::get("transactions/{id}/categories", "Transaction\TransactionController@transactionIndex");
Route::get("transactions/{id}/products", "Transaction\TransactionController@transactionIndex");
Route::get("transactions/{id}/buyers", "Transaction\TransactionController@transactionIndex");
Route::get("transactions/{id}/sellers", "Transaction\TransactionController@transactionIndex");
Route::resource("transactions", "Transaction\TransactionController", ["only" => ["index", "show"]]);

Route::delete("products/{idProduct}/categories/{idCategory}", "Product\ProductController@deleteCategory");
Route::put("products/{idProduct}/categories/{idCategory}", "Product\ProductController@putCategory");
Route::get("products/{id}/categories", "Product\ProductController@productIndex");
Route::resource("products", "Product\ProductController", ["only" => ["index", "show"]]);

Route::get("users/verify/{token}", "User\UserController@verifyUser")->name("user.verify");
Route::get("users/{id}/resend", "User\UserController@resendEmail")->name("user.resend");
//Route::get("users/{id}/admins", ["middleware" => "admin.check", "uses" => "User\UserController@indexAdmin"])->name("user.check.admin");
Route::get("users/{id}/admins", ["middleware" => "client", "uses" => "User\UserController@indexAdmin"])->name("user.check.admin");
Route::resource("users", "User\UserController", ["except" => ["create", "edit"]]);

Route::post("oauth/token", "\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken");
