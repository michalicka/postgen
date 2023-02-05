<?php

use App\Http\Controllers\ArtisanController;
use Illuminate\Support\Facades\Route;

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
    return view('welcome', [
        'api_url' => env('API_URL'),
        'api_user' => env('API_USER'),
    ]);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/store', [Postgen\Generator\Controllers\PostController::class, 'store'])->name('store');
Route::post('/rate', [Postgen\Generator\Controllers\PostController::class, 'rate'])->name('rate');

Route::get('/artisan/down', [ArtisanController::class, 'down'])->name('artisan.down');
Route::get('/artisan/deploy', [ArtisanController::class, 'deploy'])->name('artisan.deploy');

//Route::middleware(['auth'])->group(function () {
    Route::get('/posts/{id}/edit', [Postgen\Moderator\Controllers\PostController::class, 'edit'])->name('posts.edit');
//});
