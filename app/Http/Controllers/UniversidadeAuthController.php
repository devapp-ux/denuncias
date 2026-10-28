<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UniversidadeAuthController extends Controller
{
    public function create(): View
    {
        return view('universidade.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'As credenciais indicadas não são válidas.',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();

        if (! $user->ativo || (! $user->isAdmin() && (! $user->isProvedor() || ! $user->provedor?->ativo))) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Esta conta não tem acesso ativo ao sistema universitário.',
            ]);
        }

        return redirect()->intended(route('universidade.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('universidade.login');
    }
}
