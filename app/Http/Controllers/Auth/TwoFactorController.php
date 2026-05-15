<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\TwoFactorAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(
        private readonly TwoFactorAuthenticator $auth,
    ) {}

    public function setup(Request $request): View
    {
        $user = $request->user();

        if ($user->two_factor_secret === null) {
            $user->forceFill([
                'two_factor_secret' => $this->auth->gerarSecret(),
                'two_factor_recovery_codes' => $this->auth->gerarRecoveryCodes(),
            ])->save();
        }

        $url = $this->auth->urlOtpAuth(
            $user->two_factor_secret,
            $user->email,
            config('app.name', 'ClasseUp'),
        );

        return view('auth.two-factor.setup', [
            'qrUrl' => $url,
            'recoveryCodes' => $user->two_factor_recovery_codes,
        ]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate(['codigo' => 'required|string']);

        $user = $request->user();

        if ($user->two_factor_secret === null) {
            return redirect()->route('two-factor.setup')->with('error', 'Configure o 2FA primeiro.');
        }

        if (! $this->auth->validar($user->two_factor_secret, (string) $request->input('codigo'))) {
            return back()->with('error', 'Código inválido.');
        }

        $user->forceFill(['two_factor_confirmed_at' => now()])->save();

        return redirect()->route('dashboard')->with('success', '2FA ativado com sucesso.');
    }

    public function disable(Request $request): RedirectResponse
    {
        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return redirect()->route('profile.edit')->with('success', '2FA desativado.');
    }
}
