<?php

use App\Http\Controllers\SubscriberController;
use App\Http\Controllers\TokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::resource('subscribers', SubscriberController::class)->middleware('mailer-lite');

Route::get('token', [TokenController::class, 'view']);
Route::post('token', [TokenController::class, 'store']);
