<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Frontend\FrontendController;
use App\Http\Controllers\AuthTokenController;
use App\Http\Controllers\AuthLogoutController;
use App\Http\Controllers\Api\Posts\PostController;
use App\Http\Controllers\Api\Users\UserController;
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

Route::prefix('frontend')->group(function(){
   Route::get('/posts', [FrontendController::class, 'index'])->name('frontend.posts');
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/posts/like/{post}', [FrontendController::class, 'likePost'])->name('frontend.postLike');
        Route::post('/posts/comment/{post}', [FrontendController::class, 'commentPost'])->name('frontend.postComment');
    });
});

Route::prefix('auth')->group(function () {
    Route::post('/login', AuthTokenController::class)->name('login');
    Route::post('/logout', AuthLogoutController::class)->name('logout')->middleware(['auth:sanctum']);
});
Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'store'])->name('users.store');
});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
        Route::match(['put', 'patch'], '/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('posts')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('posts.index');
        Route::post('/', [PostController::class, 'store'])->name('posts.store');
        Route::get('/{post}', [PostController::class, 'show'])->name('posts.show');
        Route::match(['put', 'patch'], '/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    });
});
