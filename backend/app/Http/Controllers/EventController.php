<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Registration;
use App\Models\Event;
use App\Models\Checkin;
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

    public function checkin($event_id, $registration_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $event = Event::find($event_id);
        if (!$event) {
            return response()->json(['error' => 'Evento não encontrado'], 404);
        }

        $registration = Registration::find($registration_id);
        if (!$registration || $registration->event_id != $event_id) {
            return response()->json(['error' => 'Inscrição não encontrada para este evento'], 404);
        }

        $existingCheckin = Checkin::where('registration_id', $registration_id)->first();
        if ($existingCheckin) {
            return response()->json(['error' => 'Usuário já realizou check-in'], 409);
        }

        $checkin = new Checkin();
        $checkin->registration_id = $registration_id;
        $checkin->user_id = $registration->user_id;
        $checkin->event_id = $event_id;
        $checkin->checkin_time = now();
        $checkin->save();

        return response()->json(['message' => 'Check-in realizado com sucesso'], 201);
    }

    public function getEventsWithRegistrationsAndCheckins()
    {
        $events = Event::with(['registrations.user', 'registrations.checkin'])->get();

        $events->each(function ($event) {
            $event->registrations->each(function ($registration) {
                $registration->isCheckedIn = $registration->checkin ? true : false;

                unset($registration->checkin);
            });
        });

        return response()->json($events);
    }
}
