<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Chamada;
use App\Models\Avaliacao;
use App\Models\Professor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AlunoApiController extends Controller
{
    /**
     * Retorna dados detalhados de um aluno para o professor
     * Inclui verificação de segurança para garantir que o professor
     * só acesse alunos de suas turmas
     */
    public function show(Request $request, Aluno $aluno): JsonResponse
    {
        $professor = $this->obterProfessorAutenticado();
        
        // Verificar se o professor tem acesso a este aluno
        $this->verificarAcessoAoAluno($professor, $aluno);
        
        // Buscar dados completos do aluno
        $dadosAluno = $this->obterDadosCompletosAluno($aluno, $professor);
        
        return response()->json([
            'success' => true,
            'data' => $dadosAluno
        ]);
    }
    
    /**
     * Retorna estatísticas de frequência do aluno para as disciplinas do professor
     */
    public function frequencia(Request $request, Aluno $aluno): JsonResponse
    {
        $professor = $this->obterProfessorAutenticado();
        
        // Verificar se o professor tem acesso a este aluno
        $this->verificarAcessoAoAluno($professor, $aluno);
        
        $disciplinaId = $request->get('disciplina_id');
        $dataInicio = $request->get('data_inicio', now()->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        
        $frequencia = $this->obterEstatisticasFrequencia($aluno, $professor, $disciplinaId, $dataInicio, $dataFim);
        
        return response()->json([
            'success' => true,
            'data' => $frequencia
        ]);
    }
    
    /**
     * Retorna notas/avaliações do aluno para as disciplinas do professor
     */
    public function avaliacoes(Request $request, Aluno $aluno): JsonResponse
    {
        $professor = $this->obterProfessorAutenticado();
        
        // Verificar se o professor tem acesso a este aluno
        $this->verificarAcessoAoAluno($professor, $aluno);
        
        $disciplinaId = $request->get('disciplina_id');
        
        $avaliacoes = $this->obterAvaliacoesAluno($aluno, $professor, $disciplinaId);
        
        return response()->json([
            'success' => true,
            'data' => $avaliacoes
        ]);
    }
    
    /**
     * Obter professor autenticado
     */
    private function obterProfessorAutenticado(): Professor
    {
        $professor = auth()->user()->professor;
        
        if (!$professor) {
            abort(403, 'Professor não encontrado');
        }
        
        return $professor;
    }
    
    /**
     * Verificar se o professor tem acesso ao aluno
     * (aluno deve estar em uma turma onde o professor ministra alguma disciplina)
     */
    private function verificarAcessoAoAluno(Professor $professor, Aluno $aluno): void
    {
        $temAcesso = DB::table('professor_disciplina_turma')
            ->where('professor_id', $professor->id)
            ->where('turma_id', $aluno->turma_id)
            ->exists();
            
        if (!$temAcesso) {
            abort(403, 'Você não tem permissão para acessar dados deste aluno.');
        }
    }
    
    /**
     * Obter dados completos do aluno
     */
    private function obterDadosCompletosAluno(Aluno $aluno, Professor $professor): array
    {
        // Carregar relacionamentos necessários
        $aluno->load(['turma', 'user']);
        
        // Obter disciplinas que o professor ministra para esta turma
        $disciplinasProfessor = DB::table('professor_disciplina_turma')
            ->join('disciplinas', 'professor_disciplina_turma.disciplina_id', '=', 'disciplinas.id')
            ->where('professor_disciplina_turma.professor_id', $professor->id)
            ->where('professor_disciplina_turma.turma_id', $aluno->turma_id)
            ->select('disciplinas.id', 'disciplinas.nome', 'disciplinas.codigo')
            ->get();
            
        // Obter estatísticas gerais de frequência
        $estatisticasFrequencia = $this->obterEstatisticasFrequenciaGeral($aluno, $professor);
        
        // Obter últimas presenças/faltas
        $ultimasPresencas = $this->obterUltimasPresencas($aluno, $professor, 5);
        
        return [
            'id' => $aluno->id,
            'nome' => $aluno->nome,
            'email' => $aluno->email,
            'numero_matricula' => $aluno->numero_matricula,
            'data_nascimento' => $aluno->data_nascimento?->format('d/m/Y'),
            'idade' => $aluno->data_nascimento?->age,
            'telefone' => $aluno->telefone,
            'endereco' => $aluno->endereco,
            'status_matricula' => $aluno->status_matricula,
            'data_matricula' => $aluno->data_matricula?->format('d/m/Y'),
            'foto_perfil_url' => $aluno->foto_perfil_url,
            'turma' => [
                'id' => $aluno->turma->id,
                'nome' => $aluno->turma->nome,
                'serie' => $aluno->turma->serie,
                'turno' => $aluno->turma->turno,
                'ano_letivo' => $aluno->turma->ano_letivo
            ],
            'disciplinas_professor' => $disciplinasProfessor,
            'estatisticas_frequencia' => $estatisticasFrequencia,
            'ultimas_presencas' => $ultimasPresencas
        ];
    }
    
    /**
     * Obter estatísticas gerais de frequência do aluno
     */
    private function obterEstatisticasFrequenciaGeral(Aluno $aluno, Professor $professor): array
    {
        $disciplinasIds = DB::table('professor_disciplina_turma')
            ->where('professor_id', $professor->id)
            ->where('turma_id', $aluno->turma_id)
            ->pluck('disciplina_id');
            
        $totalChamadas = Chamada::where('matricula', $aluno->numero_matricula)
            ->whereIn('disciplina_id', $disciplinasIds)
            ->where('professor_id', $professor->id)
            ->count();
            
        $totalPresencas = Chamada::where('matricula', $aluno->numero_matricula)
            ->whereIn('disciplina_id', $disciplinasIds)
            ->where('professor_id', $professor->id)
            ->where('status', 'presente')
            ->count();
            
        $percentualFrequencia = $totalChamadas > 0 ? round(($totalPresencas / $totalChamadas) * 100, 1) : 0;
        
        return [
            'total_chamadas' => $totalChamadas,
            'total_presencas' => $totalPresencas,
            'total_faltas' => $totalChamadas - $totalPresencas,
            'percentual_frequencia' => $percentualFrequencia
        ];
    }
    
    /**
     * Obter últimas presenças do aluno
     */
    private function obterUltimasPresencas(Aluno $aluno, Professor $professor, int $limite = 5): array
    {
        $disciplinasIds = DB::table('professor_disciplina_turma')
            ->where('professor_id', $professor->id)
            ->where('turma_id', $aluno->turma_id)
            ->pluck('disciplina_id');
            
        $presencas = Chamada::with(['disciplina'])
            ->where('matricula', $aluno->numero_matricula)
            ->whereIn('disciplina_id', $disciplinasIds)
            ->where('professor_id', $professor->id)
            ->orderBy('data_chamada', 'desc')
            ->limit($limite)
            ->get();
            
        return $presencas->map(function ($presenca) {
            return [
                'data' => $presenca->data_chamada->format('d/m/Y'),
                'disciplina' => $presenca->disciplina->nome,
                'status' => $presenca->status,
                'justificada' => $presenca->justificada,
                'observacoes' => $presenca->observacoes
            ];
        })->toArray();
    }
    
    /**
     * Obter estatísticas detalhadas de frequência
     */
    private function obterEstatisticasFrequencia(Aluno $aluno, Professor $professor, ?int $disciplinaId, string $dataInicio, string $dataFim): array
    {
        $query = Chamada::where('matricula', $aluno->numero_matricula)
            ->where('professor_id', $professor->id)
            ->whereBetween('data_chamada', [$dataInicio, $dataFim]);
            
        if ($disciplinaId) {
            // Verificar se o professor ministra esta disciplina para esta turma
            $temPermissao = DB::table('professor_disciplina_turma')
                ->where('professor_id', $professor->id)
                ->where('turma_id', $aluno->turma_id)
                ->where('disciplina_id', $disciplinaId)
                ->exists();
                
            if (!$temPermissao) {
                abort(403, 'Você não tem permissão para acessar dados desta disciplina.');
            }
            
            $query->where('disciplina_id', $disciplinaId);
        } else {
            // Filtrar apenas pelas disciplinas que o professor ministra
            $disciplinasIds = DB::table('professor_disciplina_turma')
                ->where('professor_id', $professor->id)
                ->where('turma_id', $aluno->turma_id)
                ->pluck('disciplina_id');
                
            $query->whereIn('disciplina_id', $disciplinasIds);
        }
        
        $totalChamadas = $query->count();
        $totalPresencas = $query->where('status', 'presente')->count();
        $percentualFrequencia = $totalChamadas > 0 ? round(($totalPresencas / $totalChamadas) * 100, 1) : 0;
        
        return [
            'periodo' => [
                'inicio' => Carbon::parse($dataInicio)->format('d/m/Y'),
                'fim' => Carbon::parse($dataFim)->format('d/m/Y')
            ],
            'total_chamadas' => $totalChamadas,
            'total_presencas' => $totalPresencas,
            'total_faltas' => $totalChamadas - $totalPresencas,
            'percentual_frequencia' => $percentualFrequencia
        ];
    }
    
    /**
     * Obter avaliações do aluno
     */
    private function obterAvaliacoesAluno(Aluno $aluno, Professor $professor, ?int $disciplinaId): array
    {
        $query = Avaliacao::where('aluno_id', $aluno->id)
            ->whereHas('disciplina', function ($q) use ($professor, $aluno) {
                $q->whereExists(function ($subQuery) use ($professor, $aluno) {
                    $subQuery->select(DB::raw(1))
                        ->from('professor_disciplina_turma')
                        ->whereColumn('professor_disciplina_turma.disciplina_id', 'disciplinas.id')
                        ->where('professor_disciplina_turma.professor_id', $professor->id)
                        ->where('professor_disciplina_turma.turma_id', $aluno->turma_id);
                });
            });
            
        if ($disciplinaId) {
            // Verificar se o professor ministra esta disciplina para esta turma
            $temPermissao = DB::table('professor_disciplina_turma')
                ->where('professor_id', $professor->id)
                ->where('turma_id', $aluno->turma_id)
                ->where('disciplina_id', $disciplinaId)
                ->exists();
                
            if (!$temPermissao) {
                abort(403, 'Você não tem permissão para acessar avaliações desta disciplina.');
            }
            
            $query->where('disciplina_id', $disciplinaId);
        }
        
        $avaliacoes = $query->with(['disciplina'])
            ->orderBy('data_avaliacao', 'desc')
            ->get();
            
        return $avaliacoes->map(function ($avaliacao) {
            return [
                'id' => $avaliacao->id,
                'disciplina' => $avaliacao->disciplina->nome,
                'tipo_avaliacao' => $avaliacao->tipo_avaliacao,
                'descricao' => $avaliacao->descricao,
                'nota' => $avaliacao->nota,
                'nota_maxima' => $avaliacao->nota_maxima,
                'percentual' => $avaliacao->nota_maxima > 0 ? round(($avaliacao->nota / $avaliacao->nota_maxima) * 100, 1) : 0,
                'data_avaliacao' => $avaliacao->data_avaliacao?->format('d/m/Y'),
                'observacoes' => $avaliacao->observacoes
            ];
        })->toArray();
    }
}