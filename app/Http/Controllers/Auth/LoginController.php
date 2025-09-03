<?php 
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Corporativo;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('corporativos')->check()) {
            return redirect()->route('perfil.datos');
        }
        return view('index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usuario'  => ['required','string'], // correo/usuario de cred_user_1 o cred_user_2
            'password' => ['required','string'],
        ]);

        $user = $request->input('usuario');
        $pass = $request->input('password');

        // Buscar corporativo por usuario (cred_user_1 o cred_user_2)
        $corp = Corporativo::where('cred_user_1', $user)
            ->orWhere('cred_user_2', $user)
            ->first();

        if (!$corp) {
            return back()->with('error', 'Usuario no encontrado')->withInput();
        }

        $pairUsado = null;
        // Validar hash de credenciales
        if ($corp->cred_user_1 === $user && Hash::check($pass, $corp->cred_pass_1)) {
            $pairUsado = 1;
        } elseif ($corp->cred_user_2 === $user && Hash::check($pass, $corp->cred_pass_2)) {
            $pairUsado = 2;
        }

        if (!$pairUsado) {
            return back()->with('error', 'Contraseña inválida')->withInput();
        }

        // Autenticar con guard corporativos
        Auth::guard('corporativos')->login($corp, false);

        session([
            'corporativo_id'   => $corp->id,
            'corporativo_slug' => $corp->slug,
            'cred_pair'        => $pairUsado, // 1 o 2
        ]);

        $request->session()->regenerate();
        return redirect()->intended(route('perfil.datos'));
    }

    public function logout(Request $request)
    {
        Auth::guard('corporativos')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
