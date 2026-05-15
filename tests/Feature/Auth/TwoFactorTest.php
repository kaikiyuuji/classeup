<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Services\Auth\TwoFactorAuthenticator;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('auth.two_factor_required_for_admin', true);
    }

    public function test_admin_sem_2fa_e_redirecionado_para_setup(): void
    {
        $this->actingAsAdmin();

        $this->get(route('dashboard'))->assertRedirect(route('two-factor.setup'));
    }

    public function test_admin_acessa_dashboard_apos_confirmar_2fa(): void
    {
        $admin = $this->actingAsAdmin();

        // Setup gera secret e recovery codes
        $this->get(route('two-factor.setup'))->assertOk();

        $admin->refresh();
        $this->assertNotNull($admin->two_factor_secret);

        // Calcular código atual e confirmar
        $auth = new TwoFactorAuthenticator;
        $reflection = new \ReflectionMethod($auth, 'gerarCodigo');
        $reflection->setAccessible(true);
        $contador = (int) floor(time() / 30);
        $codigo = $reflection->invoke($auth, $admin->two_factor_secret, $contador);

        $this->post(route('two-factor.confirm'), ['codigo' => $codigo])
            ->assertRedirect(route('dashboard'));

        $admin->refresh();
        $this->assertNotNull($admin->two_factor_confirmed_at);
    }

    public function test_codigo_invalido_no_confirm_nao_ativa_2fa(): void
    {
        $admin = $this->actingAsAdmin();
        $this->get(route('two-factor.setup'));

        $this->post(route('two-factor.confirm'), ['codigo' => '000000'])
            ->assertSessionHas('error');

        $admin->refresh();
        $this->assertNull($admin->two_factor_confirmed_at);
    }

    public function test_professor_nao_e_forcado_para_setup(): void
    {
        $this->actingAsProfessor();

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_aluno_nao_e_forcado_para_setup(): void
    {
        $this->actingAsAluno();

        // Aluno é redirecionado, mas não para setup (tem suas próprias rotas).
        // Verificar apenas que não recebe 403 nem redirect para /two-factor.
        $response = $this->get(route('dashboard'));
        $this->assertNotSame(route('two-factor.setup'), $response->headers->get('Location'));
    }

    public function test_quando_flag_desativada_admin_nao_e_redirecionado(): void
    {
        config()->set('auth.two_factor_required_for_admin', false);
        $this->actingAsAdmin();

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_admin_pode_desativar_2fa(): void
    {
        $admin = $this->actingAsAdmin();
        $admin->forceFill([
            'two_factor_secret' => 'secretsecretsecr',
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->delete(route('two-factor.disable'))->assertRedirect();

        $admin->refresh();
        $this->assertNull($admin->two_factor_secret);
        $this->assertNull($admin->two_factor_confirmed_at);
    }
}
