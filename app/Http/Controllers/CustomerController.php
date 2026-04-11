<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\UpdateCustomerRequest;
use App\Models\Customer;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendVerificationCode;

class CustomerController extends Controller
{
    use ApiResponse;

    public function index() {
        $customers = Customer::all();
        return $this->response(true, 'Lista de clientes', $customers, null, 200);
    }
    
    public function update(UpdateCustomerRequest $request, $id) {
        $customer = Customer::findOrFail($id);
        
        if($request->user()->id !== $customer->id) {
            return $this->response(false, 'No autorizado para actualizar esta accion', null, null, 403);
        }

        $validated = $request->validated();

        if(isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $customer->update($validated);
        return $this->response(true, 'Datos actualizados correctamente', $customer, null, 200);
    }

    public function destroy(Request $request, $id) {
        $customer = Customer::findOrFail($id);
        
        if($request->user()->id !== $customer->id) {
            return $this->response(false, 'No autorizado para eliminar esta cuenta', null, null, 403);
        }

        $customer->delete();
        return $this->response(true, 'Cuenta eliminada correctamente', null, null, 200);
    }

    public function show(Request $request, $id) {
        $customer = Customer::findOrFail($id);
        
        if($request->user()->id !== $customer->id) {
            return $this->response(false, 'No autorizado para ver esta cuenta', null, null, 403);
        }

        return $this->response(true, 'Datos del cliente', $customer, null, 200);
    }

    public function forgotPassword(Request $request) {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        $code = random_int(1000, 9999);
        $customer = Customer::where('email', $request->email)->first();
        $customer->verification_code = Hash::make($code);
        $customer->verification_code_expires_at = now()->addMinutes(10);
        $customer->save();

        Mail::to($customer->email)->send(new SendVerificationCode($code));

        return $this->response(true, 'Código de verificación enviado a tu correo', null, null, 200);
    }

    public function resetPassword(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
            'new_password' => 'required|min:8|confirmed'
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

        $customer->password = Hash::make($request->new_password);
        $customer->verification_code = null;
        $customer->verification_code_expires_at = null;
        $customer->save();

        return $this->response(true, 'Contraseña actualizada correctamente', null, null, 200);
    }
}
