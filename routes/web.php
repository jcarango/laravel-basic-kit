<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Auth;
use App\Livewire\HojaDeVidaForm;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/attendance/{event}', [AttendanceController::class, 'showForm'])->name('attendance.form');
Route::post('/attendance/{event}', [AttendanceController::class, 'store'])->name('attendance.store');
Route::post('/attendance/{event}', [AttendanceController::class, 'storeAttendance'])->name('attendance.store');

Route::get('/hoja-de-vida', HojaDeVidaForm::class)->name('hoja-de-vida');
Route::view('/habeas-data', 'habeas-data')->name('habeas-data');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Google Auth Routes
Route::get('/auth/google/redirect', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback'])->name('google.callback');
