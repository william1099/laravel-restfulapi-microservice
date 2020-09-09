<?php

namespace App\Http\Controllers\Product;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;
use App\Product;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return $this->successResponse(["data" => $products], 200);
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
        $product = Product::findOrFail($id);

        return $this->successResponse(["data" => $product], 200);
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

    public function productIndex(Request $request, $id) {
        $product = Product::findOrFail($id);
        $path = $request->path();
        $segments = explode("/", $path);
        $lastSegment = $segments[count($segments) - 1];

        if($lastSegment == "categories") {

            $categories = $product->category;
            return $this->successResponse(["data" => $categories], 200);

        } 

    }

    public function putCategory(Request $request, $idProduct, $idCategory) {
        $product = Product::findOrFail($idProduct);
        $category = Category::findOrFail($idCategory);

        $product->category()->syncWithoutDetaching([$category->id]);

        return $this->successResponse(["data" => $product->category], 200);
    }

    public function deleteCategory(Request $request, $idProduct, $idCategory) {
        $product = Product::findOrFail($idProduct);
        $category = Category::findOrFail($idCategory);

        if(!$product->category()->find($idCategory)) {
            return $this->errorResponse(["error" => "produk tidak terdaftar dalam category"], 404);
        }

        $product->category()->detach([$idCategory]);

        return $this->successResponse(["data" => $product->category], 200);
    }
}
