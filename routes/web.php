<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\UserController;

// 1. Guest Routes (Only for people NOT logged in)
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect('/login'); // Redirect home to login
    });
    
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 2. Protected Routes (Only for Logged In Users)
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // We will add Station/Item routes here later...
    // specific autocomplete route
    Route::get('/stations/autocomplete', [StationController::class, 'autocomplete'])->name('stations.autocomplete');

    // your existing resource route
    Route::resource('stations', StationController::class);
    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    //stations
    Route::get('/stations', [StationController::class, 'index'])->name('stations.index');
    Route::post('/stations', [StationController::class, 'store'])->name('stations.store');
    Route::put('/stations/{station}', [StationController::class, 'update'])->name('stations.update');
    Route::delete('/stations/{station}', [StationController::class, 'destroy'])->name('stations.destroy');
    Route::get('/stations/{station}', [StationController::class, 'show'])->name('stations.show');
    //items
    Route::post('/stations/{station}/items', [ItemController::class, 'store'])->name('items.store');
    Route::put('/stations/{station}/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/stations/{station}/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    Route::post('/stations/{station}/transfer', [TransferController::class, 'store'])->name('items.transfer');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
});