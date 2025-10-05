<?php


use App\Http\Controllers\DirectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/directions', [DirectionController::class, 'index'])->name('index');
Route::post('/priorities/save', [DirectionController::class, 'savePriorities'])
    ->name('api.priorities.save');

