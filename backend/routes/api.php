<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EventController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

## GET ##

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/events', function () {
    return Event::all();
});

## POST ##
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials, true)) {
        $user = Auth::user();
        $token = $user->createToken('login')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);
});

Route::post('/register', [RegisterController::class, 'register']);

//Route::post('/events/{id}/register', [EventController::class, 'register']);
Route::middleware('auth:sanctum')->post('/events/{id}/register', [EventController::class, 'register']);

