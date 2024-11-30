<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Models\Event;
use App\Models\Checkin;
use App\Models\Registration;
use App\Models\User;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

## GET ##

Route::get('/user', function (Request $request) {
    Log::info($request->method() . ' ' . $request->path());
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/events', function (Request $request) {
    Log::info($request->method() . ' ' . $request->path());
    return Event::all();
});

Route::middleware('auth:sanctum')->get('/registrations', function (Request $request) {
    Log::info($request->method() . ' ' . $request->path());
    $user = $request->user();
    $registrations = $user->registrations()->with('event')->get();

    return response()->json($registrations);
});

Route::get('/events-with-registrations', function (Request $request) {
    Log::info($request->method() . ' ' . $request->path());
    $events = Event::with('registrations.user')->get();

    return response()->json($events);
});

Route::middleware('auth:sanctum')->get('/checkins', function (Request $request) {
    Log::info($request->method() . ' ' . $request->path());
    $user = Auth::user();
    $checkins = Checkin::where('user_id', $user->id)->get();

    return response()->json($checkins);
});

Route::get('/events-with-registrations-and-checkins', [EventController::class, 'getEventsWithRegistrationsAndCheckins']);

## POST ##
Route::post('/login', function (Request $request) {
    Log::info($request->method() . ' ' . $request->path());
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
    Log::info($request->method() . ' ' . $request->path());
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

Route::middleware('auth:sanctum')->post('/events/{event_id}/certificate', [EventController::class, 'generateCertificate']);

Route::post('/sync', function (Request $request) {
    Log::info($request->method() . ' ' . $request->path());

    $users         = $request->users;
    $registrations = $request->registrations;
    $checkins      = $request->checkins;

    foreach ($users ?? [] as $user) {
        $user = User::updateOrCreate(['email' => $user['email']], $user);
    }

    foreach ($registrations ?? [] as $registration) {
        $registration = Registration::updateOrCreate(
            [
                'event_id' => $registration['event_id'], 
                'user_id'  => User::where('email', $registration['user_email'])->first()->id
            ], 
            $registration
        );
    }

    foreach ($checkins ?? [] as $checkin) {
        $user = User::where('email', $checkin['user_email'])->first();

        $checkin['registration_id'] = Registration::where('user_id', $user->id)->where('event_id', $checkin['event_id'])->first()->id;

        Checkin::updateOrCreate(
            [
                'registration_id' => $checkin['registration_id'],
                'event_id'        => $checkin['event_id'],
                'user_id'         => $user->id
            ], 
            $checkin
        );
    }

    return response()->json(['message' => 'Data synchronized'], 201);
});

## PUT ##

Route::middleware('auth:sanctum')->put('/user', [UserController::class, 'update']);