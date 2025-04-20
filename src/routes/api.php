<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;

    Route::prefix('authors')->group(function () {
        Route::get('/get', [AuthorController::class, 'index'])->name('authors.get');
        Route::get('/get/{id}', [AuthorController::class, 'show']);
        Route::post('/store', [AuthorController::class, 'store'])->name('author.store');
        Route::patch('/update/{id}', [AuthorController::class, 'update'])->name('author.update');
        Route::delete('/delete/{id}', [AuthorController::class, 'destroy']);
    });

    Route::prefix('books')->group(function () {
        Route::get('/get', [BookController::class, 'index'])->name('books.get');
        Route::get('/get/{id}', [BookController::class, 'show']);
        Route::post('/store', [BookController::class, 'store'])->name('book.store');
        Route::patch('/update/{id}', [BookController::class, 'update'])->name('book.update');
        Route::delete('/delete/{id}', [BookController::class, 'destroy']);

        Route::get('/search', [BookController::class, 'search'])->name('books.search');
    });
