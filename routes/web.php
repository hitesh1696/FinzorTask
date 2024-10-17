<?php

use App\Http\Controllers\LoanDetailController;
use App\Http\Controllers\ProcessDataController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/loan-details', [LoanDetailController::class, 'index'])->name('loan-details');

Route::get('/process-data', [ProcessDataController::class, 'show'])->name('process-data');
Route::post('/process-data', [ProcessDataController::class, 'process'])->name('process-data');
