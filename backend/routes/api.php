<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;

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

Route::middleware('auth:sanctum')->get('/registrations', function (Request $request) {
    $user = $request->user();
    $registrations = $user->registrations()->with('event')->get();

    return response()->json($registrations);
});

Route::middleware('auth:sanctum')->get('/events-with-registrations', function () {
    $events = Event::with('registrations.user')->get();

    return response()->json($events);
});

Route::middleware('auth:sanctum')->get('/events-with-registrations-and-checkins', [EventController::class, 'getEventsWithRegistrationsAndCheckins']);

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

//Aqui o certo seria usar o EventController, mas para simplificar, vai ficar aqui mesmo essa porra
Route::middleware('auth:sanctum')->post('events/{id}/unregister', function (Request $request) {
    $event = Event::find($request->id);

    if (!$event) {
        return response()->json(['message' => 'Event not found'], 404);
    }

    $user = Auth::user();
    $user->registrations()->delete();

    return response()->json(['message' => 'Unregistered']);
});

Route::middleware('auth:sanctum')->post('/events/{id}/register', [EventController::class, 'register']);

Route::middleware('auth:sanctum')->post('/events/{event_id}/registrations/{registration_id}/checkin', [EventController::class, 'checkin']);

## PUT ##

Route::middleware('auth:sanctum')->put('/user', [UserController::class, 'update']);