<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendVerificationCode;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Traits\ApiResponse;

class VerificationController extends Controller
{
    use ApiResponse;

    public function verifyCode(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        if ($customer->is_verified) {
            return response()->json(['message' => 'Usuario ya verificado.'], 400);
        }

        if (!Hash::check($request->code, $customer->verification_code)) {
            return response()->json(['message' => 'Código de verificación incorrecto.'], 400);
        }

        if (now()->gt($customer->verification_code_expires_at)) {
            return response()->json(['message' => 'Código de verificación expirado.'], 400);
        }

        $customer->update([
        'is_verified' => true,
        'verification_code' => null,
        'verification_code_expires_at' => null
        ]);

        return $this->response(true, 'Usuario verificado correctamente', null, null, 200);
    }

    public function resendCode(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json(['message' => 'Usuario no encontrado.'], 404);
        }

        if ($customer->is_verified) {
            return response()->json(['message' => 'Usuario ya verificado.'], 400);
        }

        $code = random_int(1000, 9999);

        $customer->update([
            'verification_code' => Hash::make($code),
            'verification_code_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($customer->email)->send(new SendVerificationCode($code));
        
        return $this->response(true, 'Código de verificación reenviado a tu correo', null, null, 200);
    }

    public function checkResetCode(Request $request){
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if(!$customer) {
            return $this->response(false, 'Usuario no encontrado', null, null, 404);
        }

        if(!Hash::check($request->code, $customer->verification_code)) {
            return $this->response(false, 'Código de verificación incorrecto', null, null, 400);
        }

        if(now()->gt($customer->verification_code_expires_at)) {
            return $this->response(false, 'Código de verificación expirado', null, null, 400);
        }

        return $this->response(true, 'Código de verificación correcto', null, null, 200);
    }
}
