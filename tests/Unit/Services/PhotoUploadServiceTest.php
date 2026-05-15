<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\PhotoUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadServiceTest extends TestCase
{
    private PhotoUploadService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->service = new PhotoUploadService;
    }

    public function test_armazena_em_pasta_e_retorna_path(): void
    {
        $file = UploadedFile::fake()->image('foto.jpg', 100, 100);

        $path = $this->service->store($file, 'alunos');

        $this->assertStringStartsWith('alunos/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_filename_e_nao_previsivel_baseado_em_ulid(): void
    {
        $file = UploadedFile::fake()->image('original.jpg');

        $path = $this->service->store($file, 'alunos');

        // Não deve conter o nome original
        $this->assertStringNotContainsString('original', $path);
        // Deve preservar a extensão
        $this->assertStringEndsWith('.jpg', $path);
        // ULID tem 26 caracteres + extensão
        $filename = basename($path);
        $this->assertSame(26 + 4, strlen($filename));
    }

    public function test_apaga_arquivo_antigo_quando_oldpath_fornecido(): void
    {
        $oldFile = UploadedFile::fake()->image('antigo.jpg');
        $oldPath = $this->service->store($oldFile, 'alunos');

        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->image('novo.jpg');
        $newPath = $this->service->store($newFile, 'alunos', $oldPath);

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($newPath);
    }

    public function test_oldpath_inexistente_nao_quebra_o_upload(): void
    {
        $file = UploadedFile::fake()->image('foto.jpg');

        $path = $this->service->store($file, 'alunos', 'alunos/inexistente.jpg');

        Storage::disk('public')->assertExists($path);
    }

    public function test_dois_uploads_no_mesmo_segundo_geram_paths_distintos(): void
    {
        $f1 = UploadedFile::fake()->image('a.jpg');
        $f2 = UploadedFile::fake()->image('b.jpg');

        $p1 = $this->service->store($f1, 'alunos');
        $p2 = $this->service->store($f2, 'alunos');

        $this->assertNotSame($p1, $p2);
    }
}
