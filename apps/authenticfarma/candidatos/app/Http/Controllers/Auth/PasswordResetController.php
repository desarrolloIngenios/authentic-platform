<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email'
        ], [
            'email.exists' => 'No encontramos una cuenta con ese correo electrónico.'
        ]);

        try {
            $token = Str::random(60);
            $user = Usuario::where('email', $request->email)->first();
              // Delete any existing tokens for this email
            DB::table('password_resets')
                ->where('email', $request->email)
                ->delete();

            // Create new reset token
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => now()
            ]);

            Mail::to($user->email)->send(new ResetPassword($user, $token));

            toastr()->success('Hemos enviado un enlace para restablecer tu contraseña a tu correo.');
            return redirect()->route('login');
        } catch (\Exception $e) {
            Log::error('Error sending password reset email: ' . $e->getMessage());
            toastr()->error('Hubo un error al enviar el correo. Por favor, intenta nuevamente.'. $e->getMessage());
            return back();
        }
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:usuarios,email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $passwordReset = DB::table('password_resets')
                ->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

            if (!$passwordReset) {
                toastr()->error('Token inválido o expirado.');
                return back();
            }

            if (now()->subHours(1)->gt($passwordReset->created_at)) {
                DB::table('password_resets')->where('email', $request->email)->delete();
                toastr()->error('El enlace ha expirado. Por favor, solicita uno nuevo.');
                return redirect()->route('password.request');
            }

            $user = Usuario::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            DB::table('password_resets')->where('email', $request->email)->delete();

            toastr()->success('Tu contraseña ha sido actualizada correctamente.');
            return redirect()->route('login');
        } catch (\Exception $e) {
            Log::error('Error resetting password: ' . $e->getMessage());
            toastr()->error('Hubo un error al restablecer tu contraseña. Por favor, intenta nuevamente.');
            return back();
        }
    }
}
