<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['middleware' => 'client.credentials'], function () use ($router){

    $router->get("/authors", "AuthorController@index");
    $router->get("/authors/{author}", "AuthorController@show");
    $router->post("/authors", "AuthorController@store");
    $router->put("/authors/{author}", "AuthorController@update");
    $router->patch("/authors/{author}", "AuthorController@update");
    $router->delete("/authors/{author}", "AuthorController@delete");


    $router->get("/books", "BookController@index");
    $router->get("/books/{book}", "BookController@show");
    $router->post("/books", "BookController@store");
    $router->put("/books/{book}", "BookController@update");
    $router->patch("/books/{book}", "BookController@update");
    $router->delete("/books/{book}", "BookController@delete");
});
