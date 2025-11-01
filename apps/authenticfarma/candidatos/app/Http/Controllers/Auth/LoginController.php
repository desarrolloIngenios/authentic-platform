<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Usuario;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $redirectTo = $request->query('redirect_to');
        return view('auth.login', compact('redirectTo'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);


        $user = Usuario::where('email', $request->email)->first();

        if ($user && $this->checkPassword($request->password, $user->password)) {
            if (!$user->enabled) {
                toastr()->error('Tu cuenta aún no ha sido activada. Por favor, revisa tu correo electrónico.');
                return back();
            }

            Auth::login($user);
            $hoja_vida = $user->hojaVida()->first();
            $candidato = $hoja_vida->candidato()->first();

            session(['user_role' => $user->roles->first()->nombre]);
            session(['user_id' => Auth::id()]);
            session(['hoja_vida' => $hoja_vida->id_hoja_vida]);

            if (!is_null($candidato)) {
                session(['candidato' => $candidato->id_candidato]);
            } else {
                session(['candidato' => 0]);
            }

            if ($user->roles->first()->nombre === 'ROLE_USER' && session('candidato') == 0) {
                toastr()->success('Inicio de sesión exitoso');
                return redirect()->route('account.index');
            } elseif ($user->roles->first()->nombre === 'ROLE_USER' && session('candidato') != 0) {
                toastr()->success('Inicio de sesión exitoso');

                if ($request->has('redirect_to')) {
                    return redirect()->to($request->redirect_to);
                }

                return redirect()->route('vacant.index');
            } elseif ($user->roles->first()->nombre === 'ROLE_ADMIN') {
                toastr()->success('Inicio de sesión exitoso');
                return redirect()->route('plan.index');
            } else {
                Auth::logout();
                session()->flush();
                toastr()->error('No tienes permisos para acceder');
                return back();
            }
        } else {
            toastr()->error('Las  credenciales ingresadas son incorrectas');
            return back();
        }
    }

    private function checkPassword($inputPassword, $storedPassword)
    {
        return password_verify($inputPassword, $storedPassword);
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect()->route('login.index');
    }
}
