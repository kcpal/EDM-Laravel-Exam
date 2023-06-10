<?php

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

// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\LoanController;

Route::get('/', [LoanController::class, 'index'])->name('loan.form');
Route::get('/show/{loanDetail}', [LoanController::class, 'show'])->name('loan.show');
Route::post('/calculate', [LoanController::class, 'calculate'])->name('loan.calculate');
