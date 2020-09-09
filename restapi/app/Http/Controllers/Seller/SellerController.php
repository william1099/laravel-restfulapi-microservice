<?php

namespace App\Http\Controllers\Seller;

use App\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellers = Seller::has("product")->get();

        return $this->successResponse(["data" => $sellers], 200);
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
            $seller = Seller::has("product")->findOrFail($id);
            return $this->successResponse(["data" => $seller], 200);

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

    public function sellerIndex(Request $request, $id) {
        $sellers = Seller::findOrFail($id);
        $path = $request->path();
        $segments = explode("/", $path);
        $lastSegment = $segments[count($segments) - 1];

        if($lastSegment == "products") {

            $products = $sellers->product;
            return $this->successResponse(["data" => $products], 200);

        } else if($lastSegment == "transactions") {

            $transactions = $sellers->product()->whereHas("transaction")
                            ->with("transaction")->get()->pluck("transaction")
                            ->collapse();
            return $this->successResponse(["data" => $transactions], 200);

        } 

    }

    public function storeProduct(Request $request, $id) {

        $seller = Seller::findOrFail($id);

        $rules = [
            "name" => "required",
            "description" => "required",
            "quantity" => "required|integer|min:1",
            "image" => "required|image"
        ];

        $this->validate($request, $rules);

        $image = $request->file("image");
        $imageName = $image->getClientOriginalName();
        Storage::disk("image")->put($imageName, file_get_contents($image));

        $fields = $request->only(["name", "description", "quantity", "image"]);
        $fields["status"] = Product::UNAVAILABLE_PRODUCT;
        $fields["image"] = $imageName;
        $fields["seller_id"] = $seller->id;

        $product = Product::create($fields);

        return $this->successResponse(["data" => $product], 201);

    }

    public function updateProduct(Request $request, $idSeller, $idProduct) {

        $seller = Seller::findOrFail($idSeller);
        $product = Product::findOrFail($idProduct);

        $this->verifySellerProduct($seller, $product);

        $rules = [
            "quantity" => "integer|min:1",
            "image" => "image",
            "status" => "in:" . Product::UNAVAILABLE_PRODUCT . "," . Product::AVAILABLE_PRODUCT
        ];

        $this->validate($request, $rules);

        $fields = $request->only(["name", "description", "quantity"]);
        
        $product = $this->checkIfChanges($product, $fields, $request);

        if($request->has("status") && $request->get("status") != $product->status) {
            
            $product->status = $request->get("status");
            
            if($product->isAvailable() && $product->category()->count() == 0) {
                return $this->errorResponse(["error" => "produk harus memiliki kategori terlebih dahulu"], 409);
            }
        }

        if($request->has("image")) {
            $image = $request->file("image");
            $imageName = $image->getClientOriginalName();
        
            Storage::disk("image")->delete($product->image);
            Storage::disk("image")->put($imageName, file_get_contents($image));
            $product->image = $imageName;
        }

        if(!$product->isDirty()) {
            return $this->errorResponse(["error" => "no value to be updated"], 422);
        }

        $product->save();

        return $this->successResponse(["data" => $product], 200);

    }

    public function deleteProduct(Request $request, $idSeller, $idProduct) {

        $seller = Seller::findOrFail($idSeller);
        $product = Product::findOrFail($idProduct);

        $this->verifySellerProduct($seller, $product);

        $product->delete();
        Storage::disk("image")->delete($product->image);

        return $this->successResponse(["message" => "produc berhasil dihapus!"], 200);
    }

    /*
    *
    *   Method 
    *   
    */
    public function checkIfChanges($product, $fields, $request) {

        foreach ($fields as $key => $value) {
            if($request->has($key) && $request->get($key) != $product[$key]) {
                $product[$key] = $request->get($key);
            }
        }
        return $product;
    }

    public function verifySellerProduct($seller, $product) {

        if(!$product->seller_id == $seller->id) {
            throw new HttpException(404, "produk bukan punya seller");
        }

    }
}
