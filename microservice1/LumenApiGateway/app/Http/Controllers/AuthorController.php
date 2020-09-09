<?php

namespace App\Http\Controllers;

use App\Services\AuthorServices;
use Illuminate\Http\Request;

class AuthorController extends ApiController
{   

    public $authorService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AuthorServices $authorServices)
    {
        $this->authorService = $authorServices;
    }

    /** 
     *  Return list of authors
     *  @return Illuminate\Http\Response
     */
    public function index() {

        return $this->successResponse($this->authorService->getAuthors());

    }

    /** 
     *  Return an author
     *  @return Illuminate\Http\Response
     */
    public function show($author) {

        return $this->successResponse($this->authorService->getAuthor($author));
        
    }

    /** 
     *  create a new author
     *  @return Illuminate\Http\Response
     */
    public function store(Request $request) {

        return $this->successResponse($this->authorService->createAuthor($request->all()), 201);

    }

    /** 
     *  Update a new author
     *  @return Illuminate\Http\Response
     */
    public function update(Request $request, $author) {

        return $this->successResponse($this->authorService->editAuthor($author, $request->all()));
    }

    /** 
     *  Delete an author
     *  @return Illuminate\Http\Response
     */
    public function delete($author) {

        return $this->successResponse($this->authorService->deleteAuthor($author));
        
    }
}
