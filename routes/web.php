<?php

use App\Http\Controllers\Home;
use App\Http\Controllers\Emailparse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// for r&d purpose
Route::get('/sendinblueemial', [Home::class,"sendinblueEmial"]);
Route::get('/getemailreports', [Home::class,"getEmailReports"]);

//live routes
Route::get('/sendEmail', [Emailparse::class,"sendEmail"]);
Route::get('/parseinbound', [Emailparse::class,"parseinbound"]);
Route::get('/outbox', [Emailparse::class,"outbox"]);
Route::get('/replies', [Emailparse::class,"replies"]);
