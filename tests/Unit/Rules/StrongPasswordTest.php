<?php

declare(strict_types=1);

namespace Tests\Unit\Rules;

use App\Rules\StrongPassword;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\TestCase;

class StrongPasswordTest extends TestCase
{
    private function validar(string $password): array
    {
        $loader = new ArrayLoader;
        $translator = new Translator($loader, 'pt_BR');
        $validator = new Validator(
            $translator,
            ['password' => $password],
            ['password' => [new StrongPassword]],
        );

        return $validator->errors()->all();
    }

    public function test_rejeita_senha_menor_que_12_caracteres(): void
    {
        $erros = $this->validar('Abc!2def');

        $this->assertNotEmpty($erros);
    }

    public function test_rejeita_sem_maiuscula(): void
    {
        $erros = $this->validar('abcdef123456!');

        $this->assertNotEmpty($erros);
    }

    public function test_rejeita_sem_minuscula(): void
    {
        $erros = $this->validar('ABCDEF123456!');

        $this->assertNotEmpty($erros);
    }

    public function test_rejeita_sem_numero(): void
    {
        $erros = $this->validar('Abcdefghijk!');

        $this->assertNotEmpty($erros);
    }

    public function test_rejeita_sem_simbolo(): void
    {
        $erros = $this->validar('Abcdef1234567');

        $this->assertNotEmpty($erros);
    }

    public function test_aceita_senha_forte(): void
    {
        $erros = $this->validar('MinhaSenh@2026Forte!');

        $this->assertEmpty($erros);
    }
}
