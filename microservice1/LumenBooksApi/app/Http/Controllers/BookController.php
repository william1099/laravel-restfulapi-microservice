<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use Illuminate\Http\Request;

class BookController extends ApiController
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

        $books = Book::all();
        return $this->successResponse($books);

    }

    /** 
     *  Return an author
     *  @return Illuminate\Http\Response
     */
    public function show($book) {

        $book = Book::findOrFail($book);

        return $this->successResponse($book);
    }

    /** 
     *  create a new author
     *  @return Illuminate\Http\Response
     */
    public function store(Request $request) {

        $rules = [
            "title" => "required|max:255",
            "description" => "required|max:255",
            "price" => "required|integer|min:1",
            "author_id" => "required|integer|min:1"
        ];

        $this->validate($request, $rules);

        $book = Book::create($request->all());

        return $this->successResponse($book, 201);

    }

    /** 
     *  Update a new author
     *  @return Illuminate\Http\Response
     */
    public function update(Request $request, $book) {

        $rules = [
            "title" => "max:255",
            "description" => "max:255",
            "price" => "integer|min:1",
            "author_id" => "integer|min:1"
        ];

        $this->validate($request, $rules);

        $book = Book::findOrFail($book);
        $book->fill($request->all());

        if($book->isClean()) {
            return $this->errorResponse("no value to be updated", 412);
        }

        $book->save();

        return $this->successResponse($book);
    }

    /** 
     *  Delete an author
     *  @return Illuminate\Http\Response
     */
    public function delete($book) {

        $book = Book::findOrFail($book);
        $book->delete();

        return $this->successResponse($book);
    }
}
