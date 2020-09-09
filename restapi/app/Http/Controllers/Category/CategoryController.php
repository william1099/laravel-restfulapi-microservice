<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class CategoryController extends ApiController
{
    public function __construct()
    {
        $this->middleware("auth:api")->except(["index"]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return $this->successResponse(["data" => $categories], 200);
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
        $rules = [
            "name" => "required",
            "description" => "required"
        ];

        $this->validate($request, $rules);

        $data = $request->all();
        $category = Category::create($data);

        return $this->successResponse(["data" => $category], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categories = Category::findOrFail($id);
        
        return $this->successResponse(["data" => $categories], 200);
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
        $category = Category::findOrFail($id);

        if($request->get("name") && $request->get("name") != $category->name) {
            $category->name = $request->get("name");
        }

        if($request->get("description") && $request->get("description") != $category->description) {
            $category->description = $request->get("description");
        }

        if(!$category->isDirty()) {
            return $this->errorResponse(["message" => "no value to be updated"], 422);
        }

        $category->save();
        
        return $this->successResponse(["data" => $category], 200);

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return $this->successResponse(["message" => "data berhasil didelete"], 200);
    }

    public function categoryIndex(Request $request, $id) {
        $category = Category::findOrFail($id);
        $path = $request->path();
        $segments = explode("/", $path);
        $lastSegment = $segments[count($segments) - 1];

        if($lastSegment == "products") {

            $products = $category->product;
            return $this->successResponse(["data" => $products], 200);

        } else if($lastSegment == "buyers") {

            $buyers = $category->product()->whereHas("transaction")
                        ->with("transaction.buyer")->get()
                        ->pluck("transaction")->collapse()
                        ->whereNotNull("buyer")->pluck("buyer")
                        ->unique("id")->values();
            return $this->successResponse(["data" => $buyers], 200);

        } else if($lastSegment == "transactions") {

            $transactions = $category->product()->whereHas("transaction")->
                            with("transaction")->get()->pluck("transaction")
                            ->collapse();
            return $this->successResponse(["data" => $transactions], 200);

        } else if($lastSegment == "sellers") {

            $sellers = $category->product()->with("seller")->get()
                        ->pluck("seller")->unique("id")->values();
            return $this->successResponse(["data" => $sellers], 200);

        }

    }
}
