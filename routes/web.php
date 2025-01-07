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
// Route::get('/search', [PostController::class, 'search'])->name('search');

Auth::routes();