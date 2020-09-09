<?php

namespace App\Services;

use App\Traits\ConsumeExternalService;

class BookServices {

    use ConsumeExternalService;
    
    public $baseUri;
    public $secret;

    public function __construct() {

        $this->baseUri = config("services.book.base_uri");
        $this->secret = config("services.book.secret");
        
    }

    // GET /books
    public function getBooks() {

        return $this->perfomRequest("GET", "/books");

    }

    // GET /books/{book}
    public function getBook($book) {

        return $this->perfomRequest("GET", "/books/{$book}");

    }

    // POST /books
    public function createBook($data) {

        return $this->perfomRequest("POST", "/books", $data);

    }

    // PUT /books/{book}
    public function editBook($book, $data) {

        return $this->perfomRequest("PUT", "/books/{$book}", $data);

    }

    // DELETE /books/{book}
    public function deleteBook($book) {

        return $this->perfomRequest("DELETE", "/books/{$book}");
        
    }

}