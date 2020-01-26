<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();
// menghapus registrasi untuk admin
Route::match(["GET", "POST"], "/register", function(){
    return redirect("/login");
})->name("register");
   

Route::get('/home', 'HomeController@index')->name('home');

//resource tambahan
Route::get('/categories/trash', 'CategoryController@trash')->name('categories.trash');
Route::get('/categories/{id}/restore', 'CategoryController@restore')->name('categories.restore');
Route::delete('/categories/{id}/delete-permanent','CategoryController@deletePermanent')->name('categories.deletePermanent');

Route::get('/ajax/categories/search', 'CategoryController@ajaxSearch');

Route::get('books/trash', 'BookController@trash')->name('books.trash');
Route::get('/books/{id}/restore', 'BookController@restore')->name('books.restore');
Route::delete('/books/{id}/delete-permanent','BookController@deletePermanent')->name('books.deletePermanent');

//resources
Route::resources([
    "categories" => "CategoryController",
    "users" => "UserController",
    "books" => "BookController",
    "orders" => "OrderController"
]);

