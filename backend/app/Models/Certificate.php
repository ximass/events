<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['registration_id', 'certificate_code', 'generated_at'];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
