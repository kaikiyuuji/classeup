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
     * Excluir foto de perfil do professor
     */
    private function excluirFotoPerfil(Professor $professor): void
    {
        if ($professor->foto_perfil && Storage::disk('public')->exists($professor->foto_perfil)) {
            Storage::disk('public')->delete($professor->foto_perfil);
        }
    }
    
    /**
     * Verificar se o professor possui relacionamentos que serão afetados pela exclusão
     */
    public function verificarRelacionamentosExistentes(Professor $professor): array
    {
        $relacionamentos = [
            'temRelacionamentos' => false,
            'detalhes' => []
        ];
        
        // Verificar chamadas
        $totalChamadas = $professor->chamadas()->count();
        if ($totalChamadas > 0) {
            $relacionamentos['temRelacionamentos'] = true;
            $relacionamentos['detalhes']['chamadas'] = [
                'total' => $totalChamadas,
                'descricao' => $totalChamadas === 1 ? '1 chamada registrada' : "{$totalChamadas} chamadas registradas"
            ];
        }
        
        // Verificar vínculos com turmas e disciplinas
        $totalVinculosTurmas = $professor->disciplinasComTurma()->count();
        if ($totalVinculosTurmas > 0) {
            $relacionamentos['temRelacionamentos'] = true;
            $turmasVinculadas = $professor->disciplinasComTurma()
                ->with(['turma:id,nome', 'disciplina:id,nome'])
                ->get()
                ->groupBy('turma.nome')
                ->map(function ($vinculos) {
                    return $vinculos->pluck('disciplina.nome')->unique()->values();
                });
                
            $relacionamentos['detalhes']['turmas'] = [
                'total' => $totalVinculosTurmas,
                'descricao' => $totalVinculosTurmas === 1 ? '1 vínculo com turma/disciplina' : "{$totalVinculosTurmas} vínculos com turmas/disciplinas",
                'detalhamento' => $turmasVinculadas
            ];
        }
        
        // Verificar vínculos diretos com disciplinas
        $totalDisciplinas = $professor->disciplinas()->count();
        if ($totalDisciplinas > 0) {
            $relacionamentos['temRelacionamentos'] = true;
            $disciplinasNomes = $professor->disciplinas()->pluck('nome')->toArray();
            $relacionamentos['detalhes']['disciplinas'] = [
                'total' => $totalDisciplinas,
                'descricao' => $totalDisciplinas === 1 ? '1 disciplina vinculada' : "{$totalDisciplinas} disciplinas vinculadas",
                'nomes' => $disciplinasNomes
            ];
        }
        
        return $relacionamentos;
    }
    
    /**
     * Excluir professor e todos os seus relacionamentos
     */
    public function excluirProfessorComRelacionamentos(Professor $professor): array
    {
        try {
            DB::beginTransaction();
            
            // Remover vínculos com turmas e disciplinas
            $professor->disciplinasComTurma()->detach();
            
            // Remover vínculos diretos com disciplinas
            $professor->disciplinas()->detach();
            
            // Excluir chamadas relacionadas
            $professor->chamadas()->delete();
            
            // Excluir foto de perfil
            $this->excluirFotoPerfil($professor);
            
            // Excluir o professor
            $professor->delete();
            
            DB::commit();
            
            return [
                'sucesso' => true,
                'mensagem' => 'Professor e todos os dados relacionados foram excluídos com sucesso.'
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'sucesso' => false,
                'mensagem' => 'Erro ao excluir professor: ' . $e->getMessage()
            ];
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