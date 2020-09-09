<?php

namespace App\Http\Controllers\Transaction;

use App\Buyer;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class TransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::all();
        return $this->successResponse(["data" => $transactions], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idBuyer, $idProduct)
    {
        $buyer = Buyer::findOrFail($idBuyer);
        $product = Product::findOrFail($idProduct);

        $rules = [
            "quantity" => "required|integer|min:1"
        ];

        $this->validate($request, $rules);

        if($buyer->id == $product->seller_id) {
            return $this->errorResponse(["error" => "buyer dan seller adalah orang yang sama"], 409);
        }

        if(!$buyer->isVerified()) {
            return $this->errorResponse(["error" => "buyer tidak verified"], 409);
        }

        if(!$product->seller->isVerified()) {
            return $this->errorResponse(["error" => "seller tidak verified"], 409);
        }

        if(!$product->isAvailable()) {
            return $this->errorResponse(["error" => "produk tidak tersedia"], 409);
        }

        if($request->quantity > $product->quantity) {
            return $this->errorResponse(["error" => "jumlah produk tidak cukup"], 409);
        }

        return DB::transaction(function() use ($product, $buyer, $request) {

            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                "quantity" => $request->quantity,
                "buyer_id" => $buyer->id,
                "product_id" => $product->id
            ]);

            return $this->successResponse(["data" => $transaction], 201);

        });

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);

        return $this->successResponse(["data" => $transaction], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function transactionIndex(Request $request, $id) {
        $transaction = Transaction::findOrFail($id);
        $path = $request->path();
        $segments = explode("/", $path);
        $lastSegment = $segments[count($segments) - 1];

        if($lastSegment == "categories") {
            $categories = $transaction->product->category;
            return $this->successResponse(["data" => $categories], 200);
        } else if($lastSegment == "products") {
            $products = $transaction->product;
            return $this->successResponse(["data" => $products], 200);
        } else if($lastSegment == "buyers") {
            $buyers = $transaction->buyer;
            return $this->successResponse(["data" => $buyers], 200);
        } else if($lastSegment == "sellers") {
            $sellers = $transaction->product->seller;
            return $this->successResponse(["data" => $sellers], 200);
        }

    }
}
