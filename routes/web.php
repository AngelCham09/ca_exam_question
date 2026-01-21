<?php

use App\Http\Controllers\FundController;
use App\Http\Controllers\GraphController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/instructions', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('investors')->group(function () {
    Route::get("/", [\App\Http\Controllers\InvestorController::class, 'index'])->name('investors.index');
    Route::get("create", [\App\Http\Controllers\InvestorController::class, 'create'])->name('investors.create');
    Route::post("store", [\App\Http\Controllers\InvestorController::class, 'store'])->name('investors.store');
    Route::get("{investor}/edit", [\App\Http\Controllers\InvestorController::class, 'edit'])->name('investors.edit');
    Route::put("{investor}", [\App\Http\Controllers\InvestorController::class, 'update'])->name('investors.update');
});

Route::get("funds", [FundController::class, 'index'])->name('funds.index');;
Route::get("investments", [InvestmentController::class, 'index'])->name('investments.index');
Route::get("graph", [GraphController::class, 'index'])->name('graph.index');
Route::get('/exam', function () {
    return view('exam.index');
})->name('exam.index');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
