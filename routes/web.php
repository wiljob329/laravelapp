<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [EController::class, "homepage"]);

Route::get('/about', [EController::class, "aboutpage"]);

Route::post('/register', [UserController::class, "register"]);
