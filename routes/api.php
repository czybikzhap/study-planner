<?php


use App\Http\Controllers\Api\DirectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/priorities/save', [DirectionController::class, 'savePriorities'])
    ->name('api.priorities.save');
