<?php

use App\Http\Controllers\Admin\ImpersonationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/impersonation/leave', [ImpersonationController::class, 'leave'])
    ->middleware('auth')
    ->name('impersonation.leave');
