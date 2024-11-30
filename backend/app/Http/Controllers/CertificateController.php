<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public static function generateCertificateCode()
    {
        $certificateCode = Str::uuid()->toString();

        return $certificateCode;
    }
}
