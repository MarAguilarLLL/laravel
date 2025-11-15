<?php

use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test',function(){
    return response([
        'message' => 'Api esta funcionando'
    ], 200);
});

Route::post('register', [AuthenticationController::class, 'register']);