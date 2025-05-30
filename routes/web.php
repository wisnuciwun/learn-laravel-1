<?php

use App\Http\Controllers\PagesController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::options('{any}', function () {
    return response()->json([], 200, [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization',
    ]);
})->where('any', '.*');


Route::get('/portofolio', function () {
    // return view('pages/portofolio'); or you can use this
    return view('pages.portofolio');
})->name('postingan');

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/routing/{id}/{task}', function ($id, $task) {
//     return 'Page is about ' . $id . " and he/she still doing " . $task;
// });

// Route::get('/trialcontroller', [PagesController::class, 'index']); // how to call controller

Route::get('/', [PagesController::class, 'welcome']);
// Auth::routes(); it might be duplicate, but im doubt to delete it

Route::resource('post', PostController::class); // how to call controller without function name

Route::get('/profile', [App\Http\Controllers\DashboardController::class, 'index'])->name('index');

Route::get('/psr/all-news', [PagesController::class, 'getAllNews']);
Route::get('/psr/news/{id}', [PagesController::class, 'getDetailNews']);
Route::get('/psr/all-stores', [PagesController::class, 'getAllStores']);
Route::get('/psr/store/{slug}', [PagesController::class, 'getStoreDetail']);
Route::post('/psr/save-store', [PagesController::class, 'postNewStore']);
Route::get('/psr/img/{imageName}', [PagesController::class, 'showImg']);
Route::get('/psr/img/01kk/{imageName}', [PagesController::class, 'showImgKK']);
Route::post('/psr/store/check-keypass', [PagesController::class, 'checkKeypass']);
Route::get('/psr/store/delete/{slug}', [PagesController::class, 'deleteStore']);
Route::post('/psr/store/edit', [PagesController::class, 'editStore']);
Route::post('/psr/save-news', [PagesController::class, 'postNews']);
Route::post('/psr/upload-kk', [PagesController::class, 'uploadKK']);
Route::get('/psr/search-kk', [PagesController::class, 'searchKK']);

// Route::get('/search', [PostController::class, 'search'])->name('search');

Auth::routes();