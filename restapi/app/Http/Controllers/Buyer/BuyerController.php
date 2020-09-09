<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BuyerController extends ApiController
{

    public function __construct() {

       $this->middleware("auth:api");
      
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buyers = Buyer::has("transaction")->get();

        return $this->successResponse(["data" => $buyers], 200);
        
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $buyer = Buyer::has("transaction")->findOrFail($id);
            return $this->successResponse(["data" => $buyer], 200);

        } catch(ModelNotFoundException $e) {
            return $this->errorResponse(["error" => "OOps data tidak ditemukan"], 409);
        }

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

    public function buyerIndex(Request $request, $id) {
        $buyer = Buyer::findOrFail($id);
        $path = $request->path();
        $segments = explode("/", $path);
        $lastSegment = $segments[count($segments) - 1];

        $user = $request->user();
        if(!$user->can("view", $buyer)) {
            throw new AuthorizationException();    
        }
        if($lastSegment == "categories") {
            $categories = $buyer->transaction()->with("product.category")->get()
            ->pluck("product.category")->collapse()->unique();
            return $this->successResponse(["data" => $categories], 200);
        } else if($lastSegment == "products") {
            $products = $buyer->transaction()->with("product")->get()->pluck("product")
                        ->unique("id");
            return $this->successResponse(["data" => $products], 200);
        } else if($lastSegment == "transactions") {
            $transaction = $buyer->transaction;
            return $this->successResponse(["data" => $transaction], 200);
        } else if($lastSegment == "sellers") {
            $sellers = $buyer->transaction()->with("product.seller")->get()
                        ->pluck("product.seller")->unique("id");
            return $this->successResponse(["data" => $sellers], 200);
        }

    }
}
