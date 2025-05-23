<?php

use App\Http\Controllers\backend\MenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Backend as Backend;


Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    // Menu Routes
    Route::resource('/menus',Backend\MenuController::class);
    Route::get('menus-dt', [Backend\MenuController::class,'dataTable'])->name('menus-datatable');
});
