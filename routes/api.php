<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/list-event', [EventController::class, 'list'])
    ->name('event.list');

Route::post('/save-event', [EventController::class, 'save'])
    ->name('event.save');

Route::delete('/delete-event', [EventController::class, 'delete'])
    ->name('event.delete');
