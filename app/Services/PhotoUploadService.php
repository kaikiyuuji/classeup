<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Encapsula upload de fotos de perfil para o disco `public`.
 *
 * - Filename usa ULID (26 chars) — não previsível (não é `time().uniqid()`).
 * - Se `$oldPath` for fornecido e existir, é apagado antes de gravar o novo
 *   arquivo (evita órfãos no disco quando um aluno troca de foto).
 */
class PhotoUploadService
{
    public function __construct(
        private readonly string $disk = 'public',
    ) {}

    public function store(UploadedFile $file, string $folder, ?string $oldPath = null): string
    {
        if ($oldPath !== null && Storage::disk($this->disk)->exists($oldPath)) {
            Storage::disk($this->disk)->delete($oldPath);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $filename = Str::ulid()->toBase32().'.'.$extension;

        $stored = $file->storeAs($folder, $filename, $this->disk);

        return $stored;
    }
}
