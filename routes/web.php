<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
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

Route::resource('/location', LocationController::class);

Route::get('/dashboard', function () {
    $locations = Auth::user()->locations->all();
    return view('dashboard', compact('locations'));
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
