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
    return view('app', [
        'app' => 'app',
    ]);
});

Auth::routes();

Route::post('/store', [Postgen\Generator\Controllers\PostController::class, 'store'])->name('store');
Route::post('/rate', [Postgen\Generator\Controllers\PostController::class, 'rate'])->name('rate');

Route::get('/artisan/down', [ArtisanController::class, 'down'])->name('artisan.down');
Route::get('/artisan/deploy', [ArtisanController::class, 'deploy'])->name('artisan.deploy');

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');
    Route::get('/posts/{id}/edit', [Postgen\Moderator\Controllers\PostController::class, 'edit'])->name('posts.edit');
    Route::get('/posts/{id}/preview', [Postgen\Moderator\Controllers\PostController::class, 'preview'])->name('posts.preview');
    Route::post('/posts/{id}/preview', [Postgen\Moderator\Controllers\PostController::class, 'preview'])->name('posts.preview');

    Route::get('/wizard', [Postgen\Wizard\Controllers\WizardController::class, 'index'])->name('wizard.index');
    Route::get('/sites', [App\Http\Controllers\AdminController::class, 'sites'])->name('admin.sites');
});

Route::get('/wiki/', [Postgen\UI\Controllers\ViewController::class, 'index'])->name('index');
Route::get('/wiki/{slug}/', [Postgen\UI\Controllers\ViewController::class, 'get'])->name('get');
Route::get('/archive/{year}/{month}/', [Postgen\UI\Controllers\ViewController::class, 'archive'])->name('archive');
Route::get('/author/{author}', [Postgen\UI\Controllers\ViewController::class, 'author'])->name('author');
Route::get('/category/{slug}', [Postgen\UI\Controllers\ViewController::class, 'category'])->name('category');
Route::get('/tag/{slug}', [Postgen\UI\Controllers\ViewController::class, 'tag'])->name('tag');
