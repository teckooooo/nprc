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
            return redirect()->route('mis-datos');
        }
        return view('index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usuario'  => ['required','string'], // SOLO usuario (cred_user_1/2)
            'password' => ['required','string'],
        ]);

        $user = $request->input('usuario');
        $pass = $request->input('password');

        $corp = Corporativo::where('cred_user_1', $user)
                ->orWhere('cred_user_2', $user)
                ->first();

        if (!$corp) {
            return back()->with('error', 'Usuario no encontrado')->withInput();
        }

        $pairUsado = null;
        if ($corp->cred_user_1 === $user && $corp->cred_pass_1 === $pass) {
            $pairUsado = 1;
        } elseif ($corp->cred_user_2 === $user && $corp->cred_pass_2 === $pass) {
            $pairUsado = 2;
        }

        if (!$pairUsado) {
            return back()->with('error', 'Contraseña inválida')->withInput();
        }

        // ✅ nombre correcto del guard
        Auth::guard('corporativos')->login($corp, false);

        session([
            'corporativo_id'   => $corp->id,
            'corporativo_slug' => $corp->slug,
            'cred_pair'        => $pairUsado, // 1 ó 2
        ]);

        $request->session()->regenerate();
        return redirect()->intended(route('mis-datos'));
    }

    public function logout(Request $request)
    {
        // ✅ nombre correcto del guard
        Auth::guard('corporativos')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
