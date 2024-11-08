<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Database\Factories\EventFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'start_date', 'end_date', 'certificate_template'];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function checkins()
    {
        return $this->hasMany(Checkin::class);
    }

    protected static function newFactory()
    {
        return EventFactory::new();
    }
}
