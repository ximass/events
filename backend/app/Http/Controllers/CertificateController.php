<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Certificate;

class CertificateController extends Controller
{
    public static function generateCertificateCode()
    {
        $certificateCode = Str::uuid()->toString();

        return $certificateCode;
    }

    public function validateCertificate(Request $request)
    {
        $request->validate([
            'certificate_code' => 'required|string',
        ]);

        $certificateCode = $request->input('certificate_code');

        $certificate = Certificate::where('certificate_code', $certificateCode)
            ->with(['registration.user', 'registration.event'])
            ->first();

        if ($certificate) {
            return response()->json([
                'message' => 'Certificado válido.',
                'user' => $certificate->registration->user->only('name', 'email'),
                'event' => $certificate->registration->event->only('title', 'start_date', 'end_date'),
                'generated_at' => $certificate->generated_at,
            ], 200);
        } else {
            return response()->json(['message' => 'Certificado não encontrado.'], 404);
        }
    }
}
