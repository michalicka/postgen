<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/faker', [Postgen\Api\Controllers\FakerController::class, 'index'])->name('api.faker');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/posts/list', [Postgen\Api\Controllers\PostApiController::class, 'list'])->name('api.posts.list');
    Route::get('/posts/{id}/get', [Postgen\Api\Controllers\PostApiController::class, 'get'])->name('api.posts.get');
    Route::post('/posts/{id}/update', [Postgen\Api\Controllers\PostApiController::class, 'update'])->name('api.posts.update');
    Route::post('/posts/{id}/remove', [Postgen\Api\Controllers\PostApiController::class, 'remove'])->name('api.posts.remove');
    Route::post('/posts/{id}/publish', [Postgen\Api\Controllers\PostApiController::class, 'publish'])->name('api.posts.publish');
});
