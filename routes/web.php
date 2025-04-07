<?php

use Illuminate\Support\Facades\Route;
//----untuk printings
use App\Http\Controllers\ResiumumPrintController;

Route::get('/resiumum/{resiumum}/print', [ResiumumPrintController::class, 'print'])->name('resiumum.print');
Route::get('/resiumum/{resiumum}/pdf', [ResiumumPrintController::class, 'pdf'])->name('resiumum.pdf');
//-------unt
Route::get('/', function () {
    return view('welcome');
});
