<?php

use App\Http\Controllers\Api\HikingSubjectApiController;
use App\Http\Controllers\Api\PlacesApiController;
use Illuminate\Support\Facades\Route;

Route::get('/hiking-subjects', [HikingSubjectApiController::class, 'index'])->name('api.hiking.index');
Route::get('/places', [PlacesApiController::class, 'index'])->name('api.places.index');
