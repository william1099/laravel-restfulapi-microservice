<?php

namespace App\Services;

use App\Traits\ConsumeExternalService;

class AuthorServices {

    use ConsumeExternalService;
    
    public $baseUri;
    public $secret;

    public function __construct() {

        $this->baseUri = config("services.author.base_uri");
        $this->secret = config("services.author.secret");

    }

    // GET /authors
    public function getAuthors() {

        return $this->perfomRequest("GET", "/authors");

    }

    // GET /authors/{author}
    public function getAuthor($author) {

        return $this->perfomRequest("GET", "/authors/{$author}");

    }

    // POST /authors
    public function createAuthor($data) {

        return $this->perfomRequest("POST", "/authors", $data);

    }

    // PUT /authors/{author}
    public function editAuthor($author, $data) {

        return $this->perfomRequest("PUT", "/authors/{$author}", $data);

    }

    // DELETE /authors/{author}
    public function deleteAuthor($author) {

        return $this->perfomRequest("DELETE", "/authors/{$author}");
        
    }
}