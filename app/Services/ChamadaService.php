<?php

namespace App\Services;

use App\Models\Aluno;
use App\Models\Chamada;
use App\Models\Disciplina;
use App\Models\Professor;
use App\Models\Turma;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ChamadaService
{
    /**
     * Processar chamada completa
     */
    public function processarChamada(Professor $professor, array $dadosValidados): array
    {
        $alunos = $this->obterAlunosAtivos($dadosValidados['turma_id']);
        
        if ($this->chamadaJaExiste($professor, $dadosValidados, $alunos)) {
            return $this->tratarChamadaExistente($dadosValidados);
        }
        
        $this->salvarChamadaCompleta($professor, $dadosValidados, $alunos);
        
        return $this->criarRespostaSucesso($dadosValidados);
    }
    
    /**
     * Obter alunos ativos da turma
     */
    private function obterAlunosAtivos(int $turmaId): Collection
    {
        return Aluno::where('turma_id', $turmaId)
            ->where('status_matricula', 'ativa')
            ->get();
    }
    
    /**
     * Verificar se chamada já existe
     */
    private function chamadaJaExiste(Professor $professor, array $dados, Collection $alunos): bool
    {
        $matriculas = $alunos->pluck('numero_matricula')->toArray();
        
        return Chamada::where('disciplina_id', $dados['disciplina_id'])
            ->where('professor_id', $professor->id)
            ->whereDate('data_chamada', $dados['data_chamada'])
            ->whereIn('matricula', $matriculas)
            ->exists();
    }
    
    /**
     * Tratar chamada existente
     */
    private function tratarChamadaExistente(array $dados): array
    {
        if (!isset($dados['confirmar_reenvio']) || !$dados['confirmar_reenvio']) {
            return [
                'sucesso' => false,
                'mensagem' => 'Já existe uma chamada registrada para esta data. Deseja substituir?'
            ];
        }
        
        return ['sucesso' => true];
    }
    
    /**
     * Salvar chamada completa
     */
    private function salvarChamadaCompleta(Professor $professor, array $dados, Collection $alunos): void
    {
        if (isset($dados['confirmar_reenvio']) && $dados['confirmar_reenvio']) {
            $this->removerChamadasExistentes($professor, $dados, $alunos);
        }
        
        $this->inserirNovasChamadas($professor, $dados, $alunos);
    }
    
    /**
     * Remover chamadas existentes
     */
    private function removerChamadasExistentes(Professor $professor, array $dados, Collection $alunos): void
    {
        $matriculas = $alunos->pluck('numero_matricula')->toArray();
        
        Chamada::where('disciplina_id', $dados['disciplina_id'])
            ->where('professor_id', $professor->id)
            ->whereDate('data_chamada', $dados['data_chamada'])
            ->whereIn('matricula', $matriculas)
            ->delete();
    }
    
    /**
     * Inserir novas chamadas usando bulk insert para otimização
     */
    private function inserirNovasChamadas(Professor $professor, array $dados, Collection $alunos): void
    {
        $presencas = $dados['presencas'] ?? [];
        $registrosChamada = $this->prepararRegistrosChamada($professor, $dados, $alunos, $presencas);
        
        // Bulk insert para otimizar performance - evita problema N+1
        Chamada::insert($registrosChamada);
    }
    
    /**
     * Preparar registros de chamada para bulk insert
     */
    private function prepararRegistrosChamada(Professor $professor, array $dados, Collection $alunos, array $presencas): array
    {
        $agora = Carbon::now();
        $registros = [];
        
        foreach ($alunos as $aluno) {
            $registros[] = [
                'matricula' => $aluno->numero_matricula,
                'disciplina_id' => $dados['disciplina_id'],
                'professor_id' => $professor->id,
                'data_chamada' => $dados['data_chamada'],
                'status' => $this->determinarStatusPresenca($aluno->numero_matricula, $presencas),
                'created_at' => $agora,
                'updated_at' => $agora
            ];
        }
        
        return $registros;
    }
    
    /**
     * Determinar status de presença do aluno
     */
    private function determinarStatusPresenca(string $matricula, array $presencas): string
    {
        return in_array($matricula, $presencas) ? 'presente' : 'falta';
    }
    
    /**
     * Criar resposta de sucesso
     */
    private function criarRespostaSucesso(array $dados): array
    {
        $turma = Turma::find($dados['turma_id']);
        $disciplina = Disciplina::find($dados['disciplina_id']);
        
        return [
            'sucesso' => true,
            'mensagem' => "Chamada da turma {$turma->nome} - {$disciplina->nome} salva com sucesso!"
        ];
    }
}