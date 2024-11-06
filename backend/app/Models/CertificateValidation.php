<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateValidation extends Model
{
    use HasFactory;

    protected $fillable = ['certificate_code', 'validated_at'];
}

