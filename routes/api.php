<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UsersController;
use App\Http\Controllers\API\ShiftController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::group([ 'middleware' => ['jwt']], function(){

    Route::controller(ShiftController::class)->group(function() {
        Route::get('/shift','index')->name('shift.index');
        Route::post('/shift','store')->name('shift.store');
        Route::get('/shift/{id}','me')->name('shift.get');
        Route::put('/shift/{id}','update')->name('shift.update');
        Route::delete('/shift/{id}','delete')->name('shift.delete');
    });

    

});