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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/app', function () {
    return view('layouts.app');
});

Route::get('/', 'PagesController@home');

Route::get('/about', 'PagesController@about');

Route::get ('/contact', 'TicketsController@create');
Route::post('/contact', 'TicketsController@store');

Route::get('/tickets', 'TicketsController@index');

Route::get('/ticket/{slug?}', 'TicketsController@show');

Route::get ('/ticket/{slug?}/edit','TicketsController@edit');
Route::post('/ticket/{slug?}/edit','TicketsController@update');

Route::post('/ticket/{slug?}/delete','TicketsController@destroy');

Route::post('/comment', 'CommentsController@newComment');

// Route::get('sendemail', function () { //test emails

//     $data = array(
//         'name' => "Learning Laravel",
//     );

//     Mail::send('emails.welcome', $data, function ($message) {

//         $message->from('yourEmail@domain.com', 'Learning Laravel');

//         $message->to('znakdmitry@gmail.com')->subject('Learning Laravel test email');

//     });

//     return "Your email has been sent successfully";

// });
Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

Route::group(array('prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'admin'), function () {
    Route::get('/', 'PagesController@home');

    Route::get('users', [ 'as' => 'admin.user.index', 'uses' => 'UsersController@index']);
    
    Route::get('roles', 'RolesController@index');
    Route::get('roles/create', 'RolesController@create');
    Route::post('roles/create', 'RolesController@store');
    
    Route::get('users/{id?}/edit', 'UsersController@edit');
    Route::post('users/{id?}/edit','UsersController@update');

    Route::get('posts', 'PostsController@index');
    Route::get('posts/create', 'PostsController@create');
    Route::post('posts/create', 'PostsController@store');
    Route::get('posts/{id?}/edit', 'PostsController@edit');
    Route::post('posts/{id?}/edit','PostsController@update');

    Route::get('categories', 'CategoriesController@index');
    Route::get('categories/create', 'CategoriesController@create');
    Route::post('categories/create', 'CategoriesController@store');
});

Route::get('/blog', 'BlogController@index');
Route::get('/blog/{slug?}', 'BlogController@show');

Route::get('/autos/getautos', 'AutosController@getAutos');
