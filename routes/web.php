<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\ChunkedUploadController;

Route::post('/import/products',[CsvImportController::class,'importProducts']);
Route::post('/upload/chunk',[ChunkedUploadController::class,'receiveChunk']);
Route::post('/upload/complete',[ChunkedUploadController::class,'complete']);
