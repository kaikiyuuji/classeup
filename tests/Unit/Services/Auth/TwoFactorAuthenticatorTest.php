<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Auth;

use App\Services\Auth\TwoFactorAuthenticator;
use PHPUnit\Framework\TestCase;

class TwoFactorAuthenticatorTest extends TestCase
{
    private TwoFactorAuthenticator $auth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = new TwoFactorAuthenticator;
    }

    public function test_gera_secret_de_16_caracteres(): void
    {
        $secret = $this->auth->gerarSecret();

        $this->assertSame(16, strlen($secret));
    }

    public function test_dois_secrets_sao_distintos(): void
    {
        $this->assertNotSame($this->auth->gerarSecret(), $this->auth->gerarSecret());
    }

    public function test_valida_codigo_gerado_no_momento(): void
    {
        $secret = $this->auth->gerarSecret();

        // Calcular o código atual usando o mesmo algoritmo
        $contador = (int) floor(time() / 30);
        $reflection = new \ReflectionMethod($this->auth, 'gerarCodigo');
        $reflection->setAccessible(true);
        $codigo = $reflection->invoke($this->auth, $secret, $contador);

        $this->assertTrue($this->auth->validar($secret, $codigo));
    }

    public function test_rejeita_codigo_invalido(): void
    {
        $secret = $this->auth->gerarSecret();

        $this->assertFalse($this->auth->validar($secret, '000000'));
    }

    public function test_rejeita_codigo_com_formato_invalido(): void
    {
        $secret = $this->auth->gerarSecret();

        $this->assertFalse($this->auth->validar($secret, 'abc123'));
        $this->assertFalse($this->auth->validar($secret, '12345'));
        $this->assertFalse($this->auth->validar($secret, '1234567'));
    }

    public function test_aceita_codigo_de_janela_anterior_dentro_da_tolerancia(): void
    {
        $secret = $this->auth->gerarSecret();
        $reflection = new \ReflectionMethod($this->auth, 'gerarCodigo');
        $reflection->setAccessible(true);
        $contadorAnterior = (int) floor(time() / 30) - 1;
        $codigoAnterior = $reflection->invoke($this->auth, $secret, $contadorAnterior);

        $this->assertTrue($this->auth->validar($secret, $codigoAnterior));
    }

    public function test_url_otpauth_inclui_secret_issuer_e_account(): void
    {
        $secret = 'ABCDEFGHIJKLMNOP';

        $url = $this->auth->urlOtpAuth($secret, 'admin@example.com', 'ClasseUp');

        $this->assertStringStartsWith('otpauth://totp/ClasseUp:admin', $url);
        $this->assertStringContainsString('issuer=ClasseUp', $url);
        $this->assertStringContainsString('secret=', $url);
    }

    public function test_gera_8_recovery_codes_distintos_por_padrao(): void
    {
        $codes = $this->auth->gerarRecoveryCodes();

        $this->assertCount(8, $codes);
        $this->assertCount(8, array_unique($codes));
    }
}
