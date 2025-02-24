<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubmissionController;


Route::post('/submission', [SubmissionController::class, 'store']);


