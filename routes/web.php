<?php

use App\Http\Controllers\PagesController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/profile', function() {
    // return view('pages.profile'); or you can use this
    return view('pages/profile');
});

Route::get('/routing/{id}/{task}', function($id, $task) {
    return 'Page is about '.$id." and he/she still doing ".$task;
});

Route::get('/trialcontroller', [PagesController::class, 'index']); // how to call controller

Route::get('/welcome', [PagesController::class, 'welcome']);
