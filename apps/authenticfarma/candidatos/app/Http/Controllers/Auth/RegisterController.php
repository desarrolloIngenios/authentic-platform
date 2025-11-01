<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\HvHojaVida;
use App\Models\User;
use App\Models\Usuario;
use App\Mail\ActivationAccount;
use App\Mail\WelcomeUser;
use Illuminate\Http\Request; // Asegúrate de importar la clase correcta
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

use function Illuminate\Log\log;

class RegisterController extends Controller
{

    public function index()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {

        try {

            DB::beginTransaction();
            $lastId = DB::table('usuarios')->max('id');
            $Id = $lastId ? $lastId + 1 : 1;

            $user = new Usuario();
            $user->id = $Id;
            $user->nombre = $request->nombre;
            $user->apellido = $request->apellido;
            $user->email = $request->email;
            $user->username = $request->email;
            $user->enabled = 0; // Set to 0 until email is verified
            $user->password = Hash::make($request->password);
            $user->confirmation_token = Str::random(60);

            $user->save();
            $user->roles()->attach(2);

            $hoja_vida = new HvHojaVida();
            $hoja_vida->id_usuario = $user->id;
            $hoja_vida->id_estado = 1;
            $hoja_vida->fecha_creacion = now();
            $hoja_vida->usuario_creacion = $request->email;
            $hoja_vida->save();
            // Mail::to($user->email)->send(new ActivationAccount($user, $user->confirmation_token));
            //Send activation email
            try {
                Mail::to($user->email)->send(new ActivationAccount($user, $user->confirmation_token));
            } catch (\Exception $mailException) {
                DB::rollBack();
                Log::error('Error al enviar el correo de activación: ' . $mailException->getMessage());
                toastr()->error("Surge un error en el servidor de correo. Por favor, inténtelo de nuevo más tarde.");
                return redirect()->back();
            }

            DB::commit();
            toastr()->success('Usuario registrado exitosamente. Por favor revisa tu correo para activar tu cuenta.');
            return redirect()->route('login');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar el usuario: ');
            toastr()->error("Surge un error al registrar el usuario. Por favor, inténtelo de nuevo.");
            return redirect()->back();
        }
    }

    public function activateAccount($token)
    {
        try {
            $user = Usuario::where('confirmation_token', $token)->first();

            if (!$user) {
                toastr()->error('Token de activación inválido.');
                return redirect()->route('login');
            }

            $user->enabled = 1;
            $user->confirmation_token = null;
            $user->save();

            // Send welcome email
            Mail::to($user->email)->send(new WelcomeUser($user));

            toastr()->success('¡Cuenta activada exitosamente! Ya puedes iniciar sesión.');
            return redirect()->route('login');
        } catch (\Exception $e) {
            Log::error('Error al activar la cuenta: ' . $e->getMessage());
            toastr()->error('Error al activar la cuenta. Por favor, intenta nuevamente.');
            return redirect()->route('login');
        }
    }
}
