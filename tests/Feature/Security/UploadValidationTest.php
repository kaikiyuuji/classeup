<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Turma;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_rejeita_arquivo_php_com_extensao_jpg(): void
    {
        $this->actingAsAdmin();
        Turma::factory()->create();

        $payload = UploadedFile::fake()->createWithContent('shell.jpg', '<?php phpinfo();');

        $response = $this->post(route('alunos.store'), [
            'nome' => 'Teste',
            'email' => 'teste@example.com',
            'cpf' => '12345678901',
            'data_nascimento' => '2010-01-01',
            'foto_perfil' => $payload,
        ]);

        $response->assertSessionHasErrors('foto_perfil');
    }

    public function test_aceita_imagem_real_jpeg(): void
    {
        $this->actingAsAdmin();
        $turma = Turma::factory()->create();

        $imagem = UploadedFile::fake()->image('foto.jpg', 200, 200);

        $response = $this->post(route('alunos.store'), [
            'nome' => 'Teste',
            'email' => 'aluno@example.com',
            'cpf' => '98765432100',
            'data_nascimento' => '2010-01-01',
            'turma_id' => $turma->id,
            'foto_perfil' => $imagem,
        ]);

        $response->assertSessionDoesntHaveErrors('foto_perfil');
    }
}
