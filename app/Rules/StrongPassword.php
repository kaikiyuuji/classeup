<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Política de senha forte aplicada em registro, reset e troca de senha.
 *
 * Regras:
 * - mínimo 12 caracteres
 * - pelo menos 1 letra maiúscula
 * - pelo menos 1 letra minúscula
 * - pelo menos 1 dígito
 * - pelo menos 1 símbolo (não-alfanumérico)
 *
 * A verificação de senha vazada via haveibeenpwned é deixada para o
 * caller compor com `Password::min(12)->uncompromised()` quando for
 * desejável (exige requisição HTTP externa).
 */
class StrongPassword implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('A senha deve ser uma string.');

            return;
        }

        if (mb_strlen($value) < 12) {
            $fail('A senha deve ter pelo menos 12 caracteres.');
        }

        if (! preg_match('/[A-Z]/', $value)) {
            $fail('A senha deve conter pelo menos uma letra maiúscula.');
        }

        if (! preg_match('/[a-z]/', $value)) {
            $fail('A senha deve conter pelo menos uma letra minúscula.');
        }

        if (! preg_match('/\d/', $value)) {
            $fail('A senha deve conter pelo menos um dígito.');
        }

        if (! preg_match('/[^A-Za-z0-9]/', $value)) {
            $fail('A senha deve conter pelo menos um caractere especial.');
        }
    }
}
