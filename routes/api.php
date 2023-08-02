<?php

use App\Http\Controllers\Api\ExchangeRateController;
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

Route::get('/exchange-rate/{date}/{currency}/{baseCurrency?}', [ExchangeRateController::class, 'get'])->where([
    'date' => '\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])',
    'currency' => '[A-Z]+',
    'baseCurrency' => '[A-Z]+',
]);