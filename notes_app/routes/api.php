<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\NoteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', RegisterController::class)->name('register');

Route::post('/login', LoginController::class)->name('login');



Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::controller(NoteController::class)->group(function () {
        Route::get('/notes', 'index')->name('api.notes.index');
        Route::post('/notes', 'store')->name('api.notes.store');
        Route::patch('/notes/{id}', 'update')->name('api.notes.update');
        Route::get('/notes/{id}', 'show')->name('api.notes.show');
        Route::delete('/notes/{id}', 'delete')->name('api.notes.delete');
    });
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//         return $request->user();
// });

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');