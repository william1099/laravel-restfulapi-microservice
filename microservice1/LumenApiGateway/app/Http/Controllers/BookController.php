<?php

namespace App\Http\Controllers;

use App\Services\AuthorServices;
use App\Services\BookServices;
use Illuminate\Http\Request;

class BookController extends ApiController
{

    public $bookService;
    public $authorService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BookServices $bookServices, AuthorServices $authorServices)
    {
        $this->bookService = $bookServices;
        $this->authorService = $authorServices;
    }

    /** 
     *  Return list of authors
     *  @return Illuminate\Http\Response
     */
    public function index() {

        return $this->successResponse($this->bookService->getBooks());
        

    }

    /** 
     *  Return an author
     *  @return Illuminate\Http\Response
     */
    public function show($book) {

        return $this->successResponse($this->bookService->getBook($book));
    }

    /** 
     *  create a new author
     *  @return Illuminate\Http\Response
     */
    public function store(Request $request) {

        $this->authorService->getAuthor($request->author_id);

        return $this->successResponse($this->bookService->createBook($request->all()), 201);
    }

    /** 
     *  Update a new author
     *  @return Illuminate\Http\Response
     */
    public function update(Request $request, $book) {

        return $this->successResponse($this->bookService->editBook($book, $request->all()));
    }

    /** 
     *  Delete an author
     *  @return Illuminate\Http\Response
     */
    public function delete($book) {

        return $this->successResponse($this->bookService->deleteBook($book));
    }
}
