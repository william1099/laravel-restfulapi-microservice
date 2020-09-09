<?php

namespace App\Http\Controllers;

use App\Author;
use Illuminate\Http\Request;

class AuthorController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /** 
     *  Return list of authors
     *  @return Illuminate\Http\Response
     */
    public function index() {

        $authors = Author::all();
        return $this->successResponse($authors);

    }

    /** 
     *  Return an author
     *  @return Illuminate\Http\Response
     */
    public function show($author) {

        $author = Author::findOrFail($author);

        return $this->successResponse($author);
    }

    /** 
     *  create a new author
     *  @return Illuminate\Http\Response
     */
    public function store(Request $request) {

        $rules = [
            "name" => "required|max:255",
            "gender" => "required|max:255|in:male,female",
            "country" => "required|max:255"
        ];

        $this->validate($request, $rules);

        $author = Author::create($request->all());

        return $this->successResponse($author, 201);

    }

    /** 
     *  Update a new author
     *  @return Illuminate\Http\Response
     */
    public function update(Request $request, $author) {

        $rules = [
            "name" => "max:255",
            "gender" => "max:255|in:male,female",
            "country" => "max:255"
        ];

        $this->validate($request, $rules);

        $author = Author::findOrFail($author);
        $author->fill($request->all());

        if($author->isClean()) {
            return $this->errorResponse("no value to be updated", 412);
        }

        $author->save();

        return $this->successResponse($author);
    }

    /** 
     *  Delete an author
     *  @return Illuminate\Http\Response
     */
    public function delete($author) {

        $author = Author::findOrFail($author);
        $author->delete();

        return $this->successResponse($author);
    }
}
