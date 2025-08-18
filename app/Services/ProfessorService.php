<?php

namespace App\Services;

use App\Models\Professor;
use App\Models\Disciplina;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfessorService
{
    /**
     * Criar um novo professor
     */
    public function criarProfessor(array $dadosValidados): Professor
    {
        if (isset($dadosValidados['foto_perfil'])) {
            $dadosValidados['foto_perfil'] = $this->processarUploadFoto($dadosValidados['foto_perfil']);
        }
        
        return Professor::create($dadosValidados);
    }
    
    /**
     * Atualizar professor existente
     */
    public function atualizarProfessor(Professor $professor, array $dadosValidados): Professor
    {
        if (isset($dadosValidados['foto_perfil'])) {
            $this->removerFotoAnterior($professor);
            $dadosValidados['foto_perfil'] = $this->processarUploadFoto($dadosValidados['foto_perfil']);
        }
        
        $professor->update($dadosValidados);
        
        return $professor;
    }
    
    /**
     * Vincular professor a uma disciplina
     */
    public function vincularDisciplina(Professor $professor, int $disciplinaId): array
    {
        if ($this->jaEstaVinculado($professor, $disciplinaId)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Professor já está vinculado a esta disciplina!'
            ];
        }
        
        $professor->disciplinas()->attach($disciplinaId);
        $disciplina = Disciplina::find($disciplinaId);
        
        return [
            'sucesso' => true,
            'mensagem' => "Professor vinculado à disciplina {$disciplina->nome} com sucesso!"
        ];
    }
    
    /**
     * Desvincular professor de uma disciplina
     */
    public function desvincularDisciplina(Professor $professor, int $disciplinaId): array
    {
        if (!$this->jaEstaVinculado($professor, $disciplinaId)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Professor não está vinculado a esta disciplina!'
            ];
        }
        
        $professor->disciplinas()->detach($disciplinaId);
        $disciplina = Disciplina::find($disciplinaId);
        
        return [
            'sucesso' => true,
            'mensagem' => "Professor desvinculado da disciplina {$disciplina->nome} com sucesso!"
        ];
    }
    
    /**
     * Processar upload de foto do professor
     */
    private function processarUploadFoto(UploadedFile $arquivo): string
    {
        $nomeArquivo = $this->gerarNomeUnicoArquivo($arquivo);
        
        return $arquivo->storeAs('professores', $nomeArquivo, 'public');
    }
    
    /**
     * Gerar nome único para o arquivo
     */
    private function gerarNomeUnicoArquivo(UploadedFile $arquivo): string
    {
        return time() . '_' . uniqid() . '.' . $arquivo->getClientOriginalExtension();
    }
    
    /**
     * Remover foto anterior do professor
     */
    private function removerFotoAnterior(Professor $professor): void
    {
        if ($professor->foto_perfil && Storage::disk('public')->exists($professor->foto_perfil)) {
            Storage::disk('public')->delete($professor->foto_perfil);
        }
    }
    
    /**
     * Verificar se professor já está vinculado à disciplina
     */
    private function jaEstaVinculado(Professor $professor, int $disciplinaId): bool
    {
        return $professor->disciplinas()->where('disciplina_id', $disciplinaId)->exists();
    }
}