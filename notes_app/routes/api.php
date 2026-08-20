<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\NoteTagsController;
use App\Http\Controllers\TagController;
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
        Route::put('/notes/{id}', 'update')->name('api.notes.update');
        Route::get('/notes/{id}', 'show')->name('api.notes.show');
        Route::delete('/notes/{id}', 'destroy')->name('api.notes.delete');
        Route::patch('/notes/{id}/pin', 'togglePin')->name('api.notes.togglePin');
    });

    Route::controller(TagController::class)->group(function () {
        Route::get('/tags', 'index')->name('api.tags.index');
        Route::post('/tags', 'store')->name('api.tags.store');
        Route::put('/tags/{id}', 'update')->name('api.tags.update');
        Route::get('/tags/{id}', 'show')->name('api.tags.show');
        Route::delete('/tags/{id}', 'destroy')->name('api.tags.destroy');
    });

    Route::controller(NoteTagsController::class)->group(function () {
        Route::get('/tags/{tagId}/notes', 'getNotesByTag')->name('api.tags.getNotesByTag');
        Route::get('/notes/{noteId}/tags', 'getNotesWithTags')->name('api.notes.getNotesWithTags');
        Route::post('/notes/{noteId}/tags', 'attachTag')->name('api.notes.attachTag');
        Route::delete('/notes/{noteId]/tags/{$tagId}', 'detachTag')->name('api.notes.detachTag');
        Route::put('/notes/{noteId}/tags', 'syncTags')->name('api.tags.syncTags');
    });
});


// Route::middleware('auth:api')->get('/user', function (Request $request) {
//         return $request->user();
// });

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');