<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function register($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $event = Event::find($id);

        if (!$event) {
            return response()->json(['error' => 'Evento não encontrado'], 404);
        }

        $existingRegistration = Registration::where('user_id', $user->id)
                                            ->where('event_id', $event->id)
                                            ->first();
        if ($existingRegistration) {
            return response()->json(['error' => 'Usuário já inscrito'], 409);
        }

        $registration = new Registration();
        $registration->user_id = $user->id;
        $registration->event_id = $event->id;
        $registration->status = 'active';
        $registration->save();

        return response()->json(['message' => 'Inscrição realizada com sucesso'], 201);
    }
}
