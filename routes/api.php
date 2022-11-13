<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes [Register and Login]
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

// Update User Profile
Route::middleware('auth:api')->prefix('/user')->group(function() {
    Route::post('/update/profile', [App\Http\Controllers\Api\user\ProfileController::class, 'updateProfile']);
    Route::post('/update/password', [App\Http\Controllers\Api\User\ProfileController::class, 'updatePassword']);
});

Route::middleware('auth:api')->group(function() {
    // Categories Resource
    Route::apiResource ('/categories', App\Http\Controllers\Api\Category\CategoryController::class);
    Route::put('/categories/{id}/restore', [App\Http\Controllers\Api\Category\CategoryController::class, 'restore']);
    Route::delete('/categories/{id}/force-delete', [App\Http\Controllers\Api\Category\CategoryController::class, 'forceDelete']);

    // Tasks Resource
    Route::apiResource ('/tasks', App\Http\Controllers\Api\Task\TaskController::class);
    Route::put('/tasks/{id}/restore', [App\Http\Controllers\Api\Task\TaskController::class, 'restore']);
    Route::delete('/tasks/{id}/force-delete', [App\Http\Controllers\Api\Task\TaskController::class, 'forceDelete']);
    Route::post('/tasks/{id}/upload-file', [App\Http\Controllers\Api\File\FileController::class, 'upload']);
    // Delete Files
    Route::delete('/files/{file}', [App\Http\Controllers\Api\File\FileController::class, 'destroy']);

    // Comments Resource
    Route::apiResource ('/comments', App\Http\Controllers\Api\Comment\CommentController::class);

    // // Limit the requests (comments) to five comments only per minute to prevent attacks
    // Route::middleware('throttle:5,1')->group(function() {});

});





// Token Test
// Route::middleware('auth:api')->get('/test_token', function() {
//     return "You are logged in!";
// });

