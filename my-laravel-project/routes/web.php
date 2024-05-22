<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelController;
Route::get('/', function () {
    return view('home');
});
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/blog', [App\Http\Controllers\HomeController::class, 'blog'])->name('blog');
Route::get('/learn', [App\Http\Controllers\HomeController::class, 'learn'])->name('learn');
Route::get('/loanReport', [App\Http\Controllers\HomeController::class, 'loan'])->name('loan');
Route::post('/upload-excel', [App\Http\Controllers\ExcelController::class, 'upload'])->name('upload.excel');
// Route::post('/generate-report', [App\Http\Controllers\ExcelController::class, 'report'])->name('generate.report');