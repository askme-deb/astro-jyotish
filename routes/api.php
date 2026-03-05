<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

Route::post('/v1/login', [LoginController::class, 'login']);
