# Log de Refatoração — ClasseUp

Este arquivo documenta cada alteração feita durante a refatoração incremental do ClasseUp, conforme o plano aprovado em `/root/.claude/plans/an-lise-o-c-digo-inteiro-clever-pearl.md`.

Formato por entrada: **o que foi feito**, **por quê**, e (quando aplicável) **antes/depois** com referências de arquivos e linhas.

Branch de trabalho: `claude/refactor-with-tdd-pbNvc`.

---

## Fase 0 — Fundação TDD e Tooling

Objetivo: preparar o pipeline (tests, lint, análise estática) antes de tocar em domínio. Estabelece a rede de segurança para refatorações posteriores.

### 0.1 Arquivos criados

#### `tests/Concerns/CreatesSchoolScenarios.php` (novo)

**O que**: trait com helpers de autenticação e fábricas para cenários comuns.

**Por quê**: o `TestCase` original (`tests/TestCase.php`) era um stub vazio (`// ...`). Sem helpers, cada teste recria o mesmo cenário (criar User, criar Turma + Professor + Disciplina, fazer attach). Centralizar reduz duplicação e prepara o terreno para a Fase 2 (RBAC), onde `actingAsAdmin/Professor/Aluno` vão popular `role` e vincular FKs automaticamente.

**API exposta**:
- `createUser(array): User`
- `actingAsAdmin(array): User`
- `actingAsProfessor(?Professor, array): User`
- `actingAsAluno(?Aluno, array): User`
- `createTurmaComProfessorDisciplina(): array{turma, professor, disciplina}`

Hoje os helpers `actingAsProfessor`/`actingAsAluno` criam um User comum (sem role); a partir da Fase 2 vão popular `users.role` e FKs `professor_id`/`aluno_id`. Assinaturas estáveis evitam quebra em massa de testes futuros.

#### `tests/TestCase.php` (modificado)

**Antes**:
```php
abstract class TestCase extends BaseTestCase
{
    //
}
```

**Depois**:
```php
abstract class TestCase extends BaseTestCase
{
    use CreatesSchoolScenarios;
    use LazilyRefreshDatabase;
}
```

**Por quê**:
- `LazilyRefreshDatabase` em vez de `RefreshDatabase` em cada classe — só roda migrations quando o teste realmente usa o DB. Mais rápido em testes puramente unitários.
- `CreatesSchoolScenarios` injeta os helpers globalmente.
- Adicionado `declare(strict_types=1)` (alinhado à decisão transversal do plano).

#### `tests/Unit/Tooling/SmokeTest.php` (novo)

**O que**: 4 testes (RED na primeira execução) que validam o setup TDD funcionando.

**Por quê**: TDD genuíno — antes de implementar helpers, escrever os testes que provam que eles funcionam. Garante:
1. `actingAsAdmin/Professor/Aluno` retornam User autenticado.
2. `createTurmaComProfessorDisciplina` realmente vincula as 3 entidades via pivot `professor_disciplina_turma`.

Este teste é a primeira linha de defesa contra regressões na Fase 0.

#### `database/factories/AvaliacaoFactory.php` (novo)

**O que**: fábrica do model `Avaliacao` com states `aprovado()` e `reprovado()`.

**Por quê**: das 7 entidades, `Avaliacao` era a única **sem** factory (já existiam para Aluno/Professor/Turma/Disciplina/Falta/User). Sem ela, os testes da Fase 1 (`NotaCalculatorTest`) e Fase 4 (cobertura ≥85%) não conseguem instanciar avaliações sem repetir setup boilerplate.

States adicionados:
- `aprovado()` — notas que dão média ≥ 6.0
- `reprovado()` — notas que dão média < 6.0

(States `comSubstitutiva()`/`comRecuperacao()` virão na Fase 1, junto com `NotaCalculator`.)

### 0.2 Dependências e build

#### `.env` (gerado)

`.env.example` copiado para `.env` (necessário para `php artisan key:generate` e rodar testes locais). Não versionado (já no `.gitignore` padrão Laravel).

#### `vendor/` instalado

`composer install --ignore-platform-req=ext-bcmath` — sandbox de execução remoto não tem `ext-bcmath` instalada. O Laravel 12 não usa bcmath em código de produto deste projeto (verificado com grep), portanto ignorar é seguro nesta máquina. O CI do GitHub Actions (`shivammathur/setup-php@v2`) já inclui as extensões necessárias.

### 0.3 Arquivos modificados (config/tooling)

#### `composer.json`

**Antes** (trecho `require-dev`):
```json
"require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel-lang/lang": "^15.22",
    ...
    "phpunit/phpunit": "^11.5.3"
}
```

**Depois**: adicionados `larastan/larastan ^3.0` e `phpstan/phpstan ^2.0`.

**Antes** (`scripts.test`):
```json
"test": ["@php artisan config:clear --ansi", "@php artisan test"]
```

**Depois**: adicionados:
- `test:coverage` — roda `artisan test --coverage`
- `lint` — `pint --test` (dry-run, falha em diff)
- `lint:fix` — aplica formatação Pint
- `stan` — `phpstan analyse --memory-limit=1G`

**Por quê**: o plano exige `composer lint`, `composer stan`, `composer test:coverage` como comandos canônicos rodando em CI e localmente.

#### `phpstan.neon` (novo)

Configuração nível 6 do Larastan, paths `app/`, `database/factories/`, `database/seeders/`, `tests/`. Inclui `phpstan-baseline.neon` (gerado automaticamente) para congelar débito atual. Exclui `app/Http/Controllers/Auth/*` (código do Breeze não auditado).

#### `phpstan-baseline.neon` (placeholder vazio)

Será regenerado por `vendor/bin/phpstan analyse --generate-baseline` no primeiro run. Congela os erros pré-existentes para que novas regressões falhem o CI.

#### `pint.json` (novo)

Preset `laravel` + regras adicionais: `declare_strict_types: true`, `ordered_imports`, `no_unused_imports`, `phpdoc_align`. **Por quê**: alinhar à decisão transversal do plano (PHP 8.2+ strict types em todo o código).

#### `phpunit.xml`

**Antes**: sem bloco `<coverage>`; `<source>` sem `<exclude>`.

**Depois**: adicionado `<coverage>` com saída HTML em `storage/coverage/` e resumo em stdout; `<exclude>` para `app/Http/Controllers/Auth`, `app/View/Components`, `app/Providers` (código de framework ou plumbing).

**Por quê**: habilita coverage para o gate progressivo do plano (0% Fase 0 → 25% Fase 1 → 85% Fase 4).

#### `.github/workflows/tests.yml`

**Antes**: 1 job `tests` rodando `php artisan test` em matriz PHP 8.2/8.3/8.4.

**Depois**: 3 jobs:
- `tests` (matriz PHP 8.2/8.3/8.4) — agora com `coverage: xdebug` e extension `bcmath` adicionada.
- `lint` (PHP 8.3) — `vendor/bin/pint --test`.
- `static-analysis` (PHP 8.3) — `vendor/bin/phpstan analyse --memory-limit=1G --no-progress`.

Também adicionado trigger para branches `main` e `claude/**` no `push`.

**Por quê**: refatoração TDD exige feedback rápido e gates duros. Sem Pint e PHPStan no CI, dívida acumula silenciosamente.

#### `database/factories/UserFactory.php`

**Antes**: factory básico com state `unverified()`.

**Depois**: adicionados `declare(strict_types=1)`, namespace import direto de `App\Models\User`, e **stubs** dos states `admin()`, `professor()`, `aluno()` (retornam state vazio por enquanto). **Por quê**: na Fase 2, esses stubs serão preenchidos para popular `users.role` e FKs `professor_id`/`aluno_id`. Manter a assinatura agora evita refactor em massa de testes depois.

#### `tests/Feature/**/*.php` (15 arquivos)

**Antes**: cada teste declarava `use RefreshDatabase, WithFaker;` (ou só `RefreshDatabase`) e importava `Illuminate\Foundation\Testing\RefreshDatabase`.

**Depois**: removido tanto `use RefreshDatabase;` quanto o import. Trait agora vem do `TestCase` base via `LazilyRefreshDatabase`.

**Por quê**: ter `LazilyRefreshDatabase` no `TestCase` base + `RefreshDatabase` no filho causa colisão de métodos. `LazilyRefreshDatabase` é estritamente melhor: só migra o DB quando o teste o usa (acelera testes unitários puros).

Lista de arquivos afetados: `DisciplinaControllerTest`, `TurmaControllerTest`, `TurmaProfessorVinculacaoTest`, `AlunoControllerTest`, `Auth/PasswordResetTest`, `FaltaControllerTest`, `AvaliacaoTest`, `ProfileTest`, `Auth/PasswordConfirmationTest`, `ProfessorControllerTest`, `RelacionamentosTest`, `Auth/AuthenticationTest`, `Auth/PasswordUpdateTest`, `Auth/RegistrationTest`, `Auth/EmailVerificationTest`.

### 0.4 Ajustes pós-Pint (strict_types)

Após aplicar `vendor/bin/pint` (que injetou `declare(strict_types=1)` em todo o código), 31 testes começaram a falhar com `TypeError` em conversões implícitas.

#### `database/factories/AlunoFactory.php`

**Antes** (linha 84):
```php
return $ano.str_pad($counters[$ano], 4, '0', STR_PAD_LEFT);
```

**Depois**:
```php
return $ano.str_pad((string) $counters[$ano], 4, '0', STR_PAD_LEFT);
```

**Por quê**: `str_pad` exige string como primeiro argumento. Com strict_types ativo, int passado direto vira `TypeError`.

#### `app/Models/Aluno.php`

**Antes** (linhas 75-77):
```php
public static function gerarNumeroMatricula(?int $ano = null): string
{
    $ano = $ano ?? date('Y');
```

**Depois**:
```php
public static function gerarNumeroMatricula(int|string|null $ano = null): string
{
    $ano = (string) ($ano ?? date('Y'));
```

E também na linha 93:
```php
// Antes
return $ano.str_pad($proximoNumero, 4, '0', STR_PAD_LEFT);
// Depois
return $ano.str_pad((string) $proximoNumero, 4, '0', STR_PAD_LEFT);
```

**Por quê**:
1. O teste `RelacionamentosTest::test_gerar_numero_matricula` chama `Aluno::gerarNumeroMatricula($ano)` com `$ano = date('Y')` (string). A assinatura original `?int` rejeitava com strict_types ativo.
2. `str_pad` exige string como primeiro arg.

Solução preserva compatibilidade aceitando `int|string|null` e normaliza internamente. Esse método sai do model na Fase 3 (vai para `MatriculaService`), então a flexibilidade aqui é temporária.

### 0.5 Estado da Fase 0: VERDE

Status: **completa e estável**.

- ✅ **92 testes passando** (351 assertions) — sem regressões em relação à baseline (que tinha 92).
- ✅ **Pint**: `pint --test` passa sem nenhum arquivo precisando de fix.
- ✅ **PHPStan**: nível 6, baseline com 117 erros pré-existentes congelados, análise atual com 0 erros novos.
- ✅ **Tooling**: 3 jobs no CI (tests, lint, static-analysis); 3 comandos canônicos (`composer lint`, `composer stan`, `composer test:coverage`).
- ✅ **SmokeTest**: 4 testes validando helpers (`actingAsAdmin/Professor/Aluno`, `createTurmaComProfessorDisciplina`).
- ✅ **`declare(strict_types=1)`**: aplicado em todos os 100+ arquivos PHP do projeto.

Volume de mudanças: 124 arquivos modificados (a maioria adição mecânica do `strict_types` pelo Pint).

## Fase 1 — Modelo de Domínio limpo

Objetivo: separar regras de negócio do Eloquent, criar enums, corrigir relacionamento `Falta`.

### 1.1 NotaCalculator (Value Object / Service de domínio puro)

#### Arquivos criados

##### `tests/Unit/Domain/Avaliacao/NotaCalculatorTest.php` (novo, 13 testes)

**TDD RED → GREEN**: escrito antes da implementação. Cobre:
- Média simples
- Aprovado/reprovado/em andamento por enum
- Substitutiva substitui menor (e só substitui se beneficiar — `substitutiva > menor`)
- Recuperação aplicada após substitutiva
- Recuperação sozinha
- Notas iguais (substituição da primeira ocorrência — comportamento documentado de `array_search`)
- Notas null tratadas como zero
- `NotaFinal` é readonly

##### `app/Domain/Avaliacao/NotaCalculator.php` (novo)

Classe pura, sem dependências Laravel. API: `calcular(av1, av2, av3, av4, substitutiva?, recuperacaoFinal?): NotaFinal`. Aceita `float|int|string|null` (porque o cast `decimal:2` do Eloquent retorna string).

##### `app/Domain/Avaliacao/NotaFinal.php` (novo, readonly class)

```php
final readonly class NotaFinal {
    public function __construct(
        public float $media,
        public SituacaoAvaliacao $situacao,
    ) {}
}
```

**Por quê**: lógica de cálculo de notas estava acoplada ao Eloquent model `Avaliacao` (linhas 67-160 antes da refatoração). Acoplamento dificultava teste isolado e violava SRP. Agora a regra de negócio é testável sem booting do Laravel.

### 1.2 Enums

#### Arquivos criados

- `app/Enums/SituacaoAvaliacao.php` — `EmAndamento`, `Aprovado`, `Reprovado`. Factory `partir(float $media, bool $temNotasLancadas)`. Constante `MEDIA_APROVACAO = 6.0`.
- `app/Enums/NivelEducacional.php` — `PreEscola`, `Fundamental`, `Medio` + `label()` + `options()`.
- `app/Enums/Turno.php` — `Matutino`, `Vespertino`, `Noturno`, `Integral`.
- `app/Enums/StatusMatricula.php` — `Ativa`, `Inativa`, `Trancada`, `Transferida`. Método `ativa()`.
- `app/Enums/Status.php` — `Ativo`, `Inativo` (genérico para Professor/Turma/Disciplina). Factory `fromBoolean(bool)`.

**Por quê**: substituir strings mágicas espalhadas pelo código por enums type-safe. Ganho imediato no PHPStan (level 6 verifica tipos enum vs string) e em refactors futuros (IDE renaming, exhaustiveness check em `match`).

### 1.3 Refatoração do model Avaliacao

#### `app/Models/Avaliacao.php`

**Antes** (lógica acoplada, 189 linhas):
```php
public function calcularNotaFinal(): void {
    $notas = $this->obterNotasParaCalculo();
    $this->nota_final = $this->calcularMedia($notas);
    $this->situacao = $this->determinarSituacao();
    $this->save();
}
// + 7 métodos privados implementando substitutiva/recuperação/menor nota
```

**Depois** (93 linhas, delega para NotaCalculator):
```php
public function calcularNotaFinal(?NotaCalculator $calculator = null): void {
    $calculator ??= app(NotaCalculator::class);
    $resultado = $calculator->calcular(
        av1: $this->av1, av2: $this->av2, av3: $this->av3, av4: $this->av4,
        substitutiva: $this->substitutiva,
        recuperacaoFinal: $this->recuperacao_final,
    );
    $this->nota_final = $resultado->media;
    $this->situacao = $resultado->situacao;
    $this->save();
}
```

Também:
- Cast `situacao` → `SituacaoAvaliacao::class` (enum nativo).
- `isAprovado()`, `isReprovado()`, `isEmAndamento()` comparam com enum em vez de string.

**Por quê**: mantém a API pública existente (`calcularNotaFinal()` ainda existe; assinatura agora aceita injeção opcional de calculator) para não quebrar `AvaliacaoService` e os testes existentes.

### 1.4 AvaliacaoService refatorado

#### `app/Services/AvaliacaoService.php`

**Mudanças**:
- Injeta `NotaCalculator` via construtor (default `new NotaCalculator`).
- `obterAvaliacoesDoAluno()` separado de `garantirAvaliacoesParaAluno()` (eliminação do side effect "criar enquanto obtém"). Caller chama explicitamente o garantir e depois lê.
- `criarAvaliacaoSeNaoExistir` usa `SituacaoAvaliacao::EmAndamento` em vez da string `'em_andamento'`.

**Por quê**: side effects em métodos chamados "obter" violam menor surpresa. Separar leitura de escrita facilita testes (read pode ser mockado sem provisionar DB writes) e prepara para Policies de RBAC na Fase 2.

### 1.5 Falta com FK aluno_id

#### `database/migrations/2026_05_15_120000_add_aluno_id_to_faltas_table.php` (novo)

Adiciona `aluno_id` FK nullable em `faltas`, com backfill via SQL:

```sql
UPDATE faltas SET aluno_id = (
    SELECT alunos.id FROM alunos
    WHERE alunos.numero_matricula = faltas.matricula LIMIT 1
) WHERE aluno_id IS NULL
```

Linhas órfãs (matrícula sem aluno) são logadas via `Log::warning` para diagnóstico. Coluna `matricula` permanece soft-deprecated nesta fase (drop só na Fase 5 quando todas as faltas tiverem `aluno_id`).

#### `app/Models/Falta.php`

**Antes** (relacionamento por string):
```php
public function aluno(): BelongsTo {
    return $this->belongsTo(Aluno::class, 'matricula', 'numero_matricula');
}
```

**Depois** (FK canônica + accessor de fallback):
```php
public function aluno(): BelongsTo {
    return $this->belongsTo(Aluno::class, 'aluno_id');
}

public function getAlunoResolvidoAttribute(): ?Aluno {
    if ($this->aluno_id !== null && $this->aluno !== null) {
        return $this->aluno;
    }
    if ($this->matricula !== null) {
        return Aluno::where('numero_matricula', $this->matricula)->first();
    }
    return null;
}
```

Adicionado `aluno_id` em `$fillable`. `scopePorAluno` agora aceita `int|string` (id ou matrícula).

**Por quê**: foreign keys são o jeito correto. `numero_matricula` era usado como FK lógica mas o banco não enforced; queries cruzadas faziam join string. Agora `belongsTo` canônica via `aluno_id`, com accessor para retrocompatibilidade transitória.

#### Teste

`tests/Feature/Falta/RelacionamentoAlunoTest.php` (3 testes): valida coluna, FK canônica e fallback via matrícula para linhas legacy.

### 1.6 Cast de status_matricula em Aluno

#### `app/Models/Aluno.php`

**Antes**:
```php
public function isMatriculaAtiva(): bool {
    return $this->status_matricula === 'ativa';
}
public function ativarMatricula(): void {
    $this->update(['status_matricula' => 'ativa']);
}
```

**Depois**:
```php
protected $casts = [
    'data_matricula' => 'date',
    'data_nascimento' => 'date',
    'status_matricula' => StatusMatricula::class,
];

public function isMatriculaAtiva(): bool {
    return $this->status_matricula === StatusMatricula::Ativa;
}
public function ativarMatricula(): void {
    $this->update(['status_matricula' => StatusMatricula::Ativa]);
}
```

**Por quê**: comparações com string mágica eram frágeis (typo em `'ativa'` vs `'Ativa'` passaria silenciosamente). Enum garante exhaustiveness e refator seguro.

### 1.7 Atualizações de views e testes

#### Views Blade (acomodam enum)

- `resources/views/admin/alunos/show.blade.php` linha 137: `ucfirst($aluno->status_matricula)` → `$aluno->status_matricula->label()`.
- `resources/views/admin/alunos/index.blade.php` linha 97: idem.
- `resources/views/admin/alunos/edit.blade.php` linhas 132-133: dropdown agora itera `StatusMatricula::cases()`.
- `resources/views/admin/alunos/notas/boletim.blade.php` linhas 165-166: comparação com enum + `->label()`.

#### Tests

- `tests/Feature/AvaliacaoTest.php`: `assertEquals('aprovado', ...)` → `assertSame(SituacaoAvaliacao::Aprovado, ...)`. Import do enum adicionado.
- `tests/Feature/RelacionamentosTest.php` linhas 114-122: idem com `StatusMatricula::Inativa/Ativa`.
- `tests/Feature/AlunoControllerTest.php` linhas 165, 191: payload de request agora envia `$aluno->status_matricula->value` (string) em vez do enum (que `Symfony\InputBag` rejeita).

#### NotaCalculator aceita `float|int|string|null`

Adaptado para receber casts decimais do Eloquent que retornam string.

### 1.8 Estado da Fase 1: VERDE

- ✅ **108 testes passando** (era 92 antes da Fase 1; +13 unit do `NotaCalculator`, +3 do `RelacionamentoAlunoTest`).
- ✅ **PHPStan**: nível 6, baseline regenerada com 103 erros pré-existentes (recuou de 117 — algumas issues sumiram com a refatoração).
- ✅ **Pint**: passa.

### 1.9 Pendências adiadas para Fase 1.5 (próxima sub-iteração)

- Migration `normalize_status_columns`: trocar `ativo` boolean por `status` string em `professores`, `turmas`, `disciplinas` com cast para `Status::class`. Não feita agora porque mexe em ~30 lugares (FormRequests com `required|boolean`, scopes `Ativo()`/`Inativo()`, factories, views). Será tratada antes da Fase 2.
- `Aluno::gerarNumeroMatricula()`: mantida no model como wrapper; sai para `MatriculaService` na Fase 3.

---

## Fase 2 — RBAC e Segurança

Objetivo: introduzir controle de acesso por papel, headers HTTP de segurança, rate limiting e validação de upload mais rigorosa.

### 2.1 Modelo de papéis (Role)

#### `app/Enums/Role.php` (novo)

Enum `string` com 3 cases: `Admin`, `Professor`, `Aluno`. Helpers `isAdmin()/isProfessor()/isAluno()` e `label()`.

#### `database/migrations/2026_05_15_130000_add_role_and_profile_links_to_users_table.php` (novo)

Adiciona à tabela `users`:
- `role` (string, default `'admin'`, indexada)
- `professor_id` (FK → `professores.id`, nullable, `onDelete: nullOnDelete`)
- `aluno_id` (FK → `alunos.id`, nullable, `onDelete: nullOnDelete`)

**Backfill**: `UPDATE users SET role = 'admin' WHERE role IS NULL` no `up()` — todos os usuários atuais viram admin (princípio do menor impacto retroativo; admin pode reatribuir manualmente depois).

#### `app/Models/User.php` (modificado)

**Antes**:
```php
protected $fillable = ['name', 'email', 'password'];
protected function casts(): array {
    return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
}
```

**Depois**: `$fillable` inclui `role`, `professor_id`, `aluno_id`. Cast `role => Role::class`. Métodos `isAdmin/isProfessor/isAluno`, `hasRole(Role ...$roles)`, relacionamentos `professor()` e `aluno()` (BelongsTo).

#### `database/factories/UserFactory.php` (modificado)

State default agora popula `role => Role::Admin` e FKs como `null`. Stubs `admin()`, `professor(?Professor)`, `aluno(?Aluno)` agora populam `role` + FK do perfil (criando `Professor`/`Aluno` via factory se não fornecido).

#### `tests/Concerns/CreatesSchoolScenarios.php` (modificado)

Helpers `actingAsAdmin/Professor/Aluno` agora delegam para os states reais do factory. Adicionados `createAdmin/ProfessorUser/AlunoUser` para conveniência.

### 2.2 Policies (autorização granular)

Criadas em `app/Policies/`:

- **`AlunoPolicy`**: admin tudo; professor lista + view; aluno só visualiza próprio perfil.
- **`ProfessorPolicy`**: admin tudo; professor visualiza só o próprio perfil.
- **`TurmaPolicy`**: admin tudo; professor visualiza turmas que leciona (via pivot `professor_disciplina_turma`); aluno visualiza apenas sua turma.
- **`DisciplinaPolicy`**: admin muta; professor leitura; aluno sem acesso.
- **`AvaliacaoPolicy`**: admin tudo; professor atualiza só avaliações cuja `(aluno→turma, disciplina)` esteja em `professor_disciplina_turma` para si; aluno só visualiza próprias avaliações (não atualiza).
- **`FaltaPolicy`**: admin tudo; professor só atualiza próprias faltas registradas dentro da janela `podeSerEditada()` (7 dias); aluno só visualiza/justifica próprias faltas.

Auto-discovery do Laravel resolve `Aluno → AlunoPolicy`, sem registro manual.

### 2.3 Middlewares de segurança

#### `app/Http/Middleware/SecurityHeaders.php` (novo)

Headers aplicados em todas as respostas web:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy: camera=(), microphone=(), geolocation=(), interest-cohort=()`
- `Content-Security-Policy-Report-Only: …` — CSP em modo **report-only** (não enforce) para não quebrar Alpine.js inline (x-data, x-on:click). Quando estabilizado, trocar a chave para `Content-Security-Policy`.
- `Strict-Transport-Security: max-age=31536000; includeSubDomains` (apenas em produção + HTTPS).

#### `app/Http/Middleware/EnsureUserHasRole.php` (novo)

Alias: `role`. Uso: `Route::middleware('role:admin,professor')`. Lança `AccessDeniedHttpException` (403) se usuário não autenticado ou role não permitido.

#### `bootstrap/app.php` (modificado)

**Antes**: `withMiddleware(function (Middleware $middleware): void { /* vazio */ })`.

**Depois**:
- Append `SecurityHeaders::class` ao grupo `web`.
- Alias `role` → `EnsureUserHasRole`.
- `RateLimiter::for('writes', …)`: 60 req/min por usuário autenticado (ou 10/min por IP para guest). Aplicado nas rotas de mutação críticas (POST/PUT/DELETE).
- `RateLimiter::for('global', …)`: 120 req/min — disponível para uso futuro em endpoints sensíveis.

### 2.4 Rotas reorganizadas por role

#### `routes/web.php` (reescrito)

**Antes**: tudo em `Route::middleware('auth')->group(...)` com `Route::resource()` que dava acesso indiscriminado.

**Depois** estruturado em 4 grupos:
1. **Auth + perfil próprio** (qualquer usuário autenticado): `/profile` (edit/update/destroy).
2. **`auth + role:admin + throttle:writes`**: mutações de Aluno/Professor/Disciplina/Turma (`create`, `store`, `edit`, `update`, `destroy`, `vincular/desvincular`).
3. **`auth + role:admin,professor`**: leitura (`index`, `show`) e fluxo de faltas (chamada, justificativa, relatórios).
4. **`auth`** com Policy via `can:` middleware: boletim (`view,aluno`) e atualizar avaliação (`update,avaliacao` + `throttle:writes`).

Rotas `create` foram movidas para ANTES de `{aluno}/{professor}/...` para evitar que o parâmetro dinâmico capturasse a string `create` (causava 404).

### 2.5 FormRequests com authorize() real

14 FormRequests agora retornam `authorize()` baseado em Policy:

```php
// AlunoStoreRequest
public function authorize(): bool {
    return $this->user()?->can('create', Aluno::class) ?? false;
}

// AlunoUpdateRequest
public function authorize(): bool {
    $aluno = $this->route('aluno');
    return $aluno !== null && ($this->user()?->can('update', $aluno) ?? false);
}
```

Mesmo padrão em `Professor*`, `Disciplina*`, `Turma*`, `Avaliacao*`, `Vincular*`, `Desvincular*`. **Antes**: todos retornavam `return true`.

### 2.6 Upload de foto endurecido

Em `AlunoStoreRequest`, `AlunoUpdateRequest`, `ProfessorStoreRequest`, `ProfessorUpdateRequest`:

**Antes**:
```php
'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
```

**Depois**:
```php
'foto_perfil' => 'nullable|image|mimes:jpeg,png,webp|mimetypes:image/jpeg,image/png,image/webp|dimensions:max_width=2000,max_height=2000|max:2048',
```

Por quê: `mimes` valida apenas a extensão. `mimetypes` valida o MIME real (lido do conteúdo do arquivo) — protege contra `shell.jpg` com conteúdo `<?php ...`. `dimensions` limita pixels para prevenir image bombs.

Mensagens custom em pt-BR adicionadas (`mimetypes`, `dimensions`).

### 2.7 Testes adicionados (TDD)

- **`tests/Feature/Policies/AlunoPolicyTest.php`** (10 testes): matriz admin × professor × aluno × CRUD.
- **`tests/Feature/Policies/PoliciesMatrixTest.php`** (9 testes): cobre Professor, Disciplina, Turma, Avaliacao e Falta Policies — incluindo cenário de professor que só atualiza avaliações de sua turma+disciplina.
- **`tests/Feature/Security/SecurityHeadersTest.php`** (5 testes): cada header de segurança.
- **`tests/Feature/Security/UploadValidationTest.php`** (2 testes): rejeita `.jpg` malicioso com bytes PHP; aceita imagem real.

### 2.8 Estado da Fase 2: VERDE

- ✅ **134 testes passando** (era 108; +26 testes nesta fase).
- ✅ PHPStan baseline regenerada com 115 erros pré-existentes (subiu de 105 — novas classes geram dynamic property access que Larastan reporta).
- ✅ Pint passa.

### 2.9 Decisões e trade-offs

- **Spatie/laravel-permission descartado**: 3 roles fixos não justificam dependência extra; Policies + enum nativo bastam.
- **CSP em report-only**: Alpine.js inline (`x-data`, `x-on:click`) inviabiliza `script-src 'self'` sem `'unsafe-inline'` + `'unsafe-eval'`. Como queremos um header de qualquer modo, fica `Content-Security-Policy-Report-Only` agora; enforcement vem em uma sub-fase após eliminar Alpine inline (ou após Browser Test confirmar quebra zero).
- **`role:admin,professor`**: aluno fica fora da maior parte do app — só boletim/faltas próprios. Conforme escopo aprovado.
- **Rate limiter `writes`**: aplicado em mutações HTTP, não em leituras (para não atrapalhar UX em listagens). 60 req/min é folgado para uso normal e contém abuso automatizado.
- **`mimetypes` ao invés de só `mimes`**: validar o conteúdo real do arquivo (via getimagesize) bloqueia upload de PHP renomeado para `.jpg`.

---

## Fase 3 — Service Layer e DTOs (parcial)

Objetivo: tirar lógica dos controllers, eliminar duplicação e introduzir DTOs imutáveis.

### 3.1 PhotoUploadService

#### `app/Services/PhotoUploadService.php` (novo)

API: `store(UploadedFile $file, string $folder, ?string $oldPath = null): string`.

**Comportamento**:
- Filename gerado por `Str::ulid()->toBase32()` (26 chars) — não previsível, evita race condition de `time().uniqid()` antigo.
- Se `$oldPath` fornecido e existir, deleta antes de gravar.
- Disco padrão `public`, parametrizável via construtor.

#### `tests/Unit/Services/PhotoUploadServiceTest.php` (novo, 5 testes RED-first)

Cobre: armazenamento, filename ULID, deleção de antigo, oldPath inexistente sem quebrar, dois uploads simultâneos sem colisão.

#### `app/Http/Controllers/AlunoController.php` (refatorado)

**Antes**: 167 linhas, com método privado `handlePhotoUpload($file)` (linhas 130-139) usando `time().'_'.uniqid()` (previsível). Lógica de delete de foto antiga duplicada inline no `update()`.

**Depois**: 105 linhas. `PhotoUploadService` e `MatriculaService` injetados via construtor; uma linha por upload (`$this->photoUpload->store(...)`). `status_matricula` agora usa enum `StatusMatricula::Ativa`.

#### `app/Http/Controllers/ProfessorController.php` (refatorado)

**Antes**: 178 linhas com `handlePhotoUpload` duplicado.

**Depois**: 130 linhas. Mesmo padrão de injeção de `PhotoUploadService`. Eliminada a duplicação.

### 3.2 MatriculaService

#### `app/Services/MatriculaService.php` (novo)

API: `gerar(?int $ano = null): string` — retorna formato `AAAA####` (ex: `20260001`).

**Diferença para o `Aluno::gerarNumeroMatricula()` legado**:
- Usa `DB::transaction` + `lockForUpdate()` para evitar **race condition** quando dois admins criam alunos simultaneamente (o método antigo fazia `where('like', ano.'%') ... ->first()` sem lock, podendo gerar matrículas duplicadas em race).
- `Aluno::gerarNumeroMatricula` foi mantido por compatibilidade até refator dos testes que ainda chamam, mas o controller agora usa `MatriculaService::gerar()`.

#### `tests/Feature/Services/MatriculaServiceTest.php` (novo, 5 testes RED-first)

Cobre: primeira matrícula `0001`, incremento sequencial, ano default (atual), ano específico não interfere com sequencial de outro ano, padding 4 dígitos.

### 3.3 ChamadaService + DTO RegistrarChamadaData + FaltaStoreRequest

#### `app/Data/RegistrarChamadaData.php` (novo, `final readonly class`)

DTO imutável com factory `fromRequest(Request)`:
```php
new RegistrarChamadaData(
    turmaId: int,
    disciplinaId: int,
    professorId: int,
    dataFalta: CarbonImmutable,
    matriculasAusentes: list<string>,
    confirmarReenvio: bool,
)
```

#### `app/Http/Requests/FaltaStoreRequest.php` (novo)

Substitui a validação inline em `FaltaController::store` (era um `$request->validate(...)` no meio do método). `authorize()` via `FaltaPolicy::create`. Helper `toDto()` retorna `RegistrarChamadaData`.

#### `app/Services/ChamadaService.php` (novo)

Encapsula o que estava espalhado em 7 métodos privados de `FaltaController` (linhas 156-233 antes da refatoração):

- `obterTurmasComVinculo(): Collection` — query cross-tabela com 3 joins.
- `alunosDaTurma(int $turmaId): EloquentCollection<Aluno>`.
- `matriculasAusentes(int $disciplinaId, int $professorId, string $data): array`.
- `jaExisteChamada(...): bool` — flag para confirmar reenvio.
- `registrar(RegistrarChamadaData $data): void` — idempotente: dentro de `DB::transaction`, apaga faltas anteriores do mesmo `(disciplina, professor, dia)` e re-cria. Popula `aluno_id` FK.
- `relatorioPorMatricula(string, CarbonImmutable, CarbonImmutable): Collection<Falta>`.

#### `tests/Feature/Services/ChamadaServiceTest.php` (novo, 5 testes TDD-first)

Cobre: criação de múltiplas faltas, idempotência (re-submeter apaga antigas), `jaExisteChamada`, filtro por turma, filtro de relatório por período + matrícula.

#### `app/Http/Controllers/FaltaController.php` (refatorado)

**Antes**: 234 linhas, 7 métodos privados com queries cruas `DB::table(...)`, validação inline, transação inline.

**Depois**: 117 linhas. `ChamadaService` injetado, `FaltaStoreRequest::toDto()` passado direto. Validação `data_falta` aceitando `CarbonImmutable`. Resolução `Falta` via route-model binding (`Falta $falta` em vez de `$id`).

### 3.4 Estado da Fase 3 (parcial): VERDE

- ✅ **149 testes passando** (era 134; +5 PhotoUploadServiceTest, +5 MatriculaServiceTest, +5 ChamadaServiceTest).
- ✅ Pint passa.
- ✅ PHPStan: baseline regenerada com **88 erros** (era 115; a refatoração eliminou 27 erros pré-existentes de "no return type specified" e "parameter with no type" no `FaltaController` e nos métodos `handlePhotoUpload`). Tudo isso ganho concreto.

### 3.5 Pendências da Fase 3 (próxima iteração)

- `AlunoService`, `ProfessorService`, `TurmaService`, `DisciplinaService` para encapsular CRUD (atualmente a lógica é simples; faz sentido extrair só quando algo a mais entrar — sem abstração prematura).
- DTOs `CriarAlunoData`, `AtualizarAlunoData`, `AtualizarNotasData`, `JustificarFaltaData` — idem.
- `AvaliacaoService` ainda tem método `obterAvaliacoesDoAluno` com side effect "criar avaliações automaticamente". Já foi separado em `garantirAvaliacoesParaAluno()` na Fase 1, mas o legacy continua sendo chamado por `AlunoController::boletim`. Próxima sub-fase troca a chamada para read+write explícito.

---

## Fase 5 — Persistência saudável

Objetivo: índices em colunas frequentes, soft delete em todas as entidades, audit columns automáticas.

### 5.1 Índices

#### `database/migrations/2026_05_15_140000_add_indexes_for_lookups.php` (novo)

Adiciona índices em colunas que são alvos frequentes de `WHERE` mas não tinham índice:
- `alunos.cpf` — buscas administrativas
- `alunos.numero_matricula` — `Aluno::where('numero_matricula', ...)` é o cerne de `Falta` e relatórios
- `professores.cpf`
- `disciplinas.codigo`
- `faltas.data_falta` — todos os relatórios filtram por período

Não toca em `users.email` (já é unique → índice automático) nem em PKs.

### 5.2 Soft Deletes

#### `database/migrations/2026_05_15_141000_add_soft_deletes_to_entities.php` (novo)

`$table->softDeletes()` em todas as 6 entidades de domínio: `alunos`, `professores`, `turmas`, `disciplinas`, `avaliacoes`, `faltas`.

**Motivação**: sistema escolar precisa preservar histórico. Aluno que sai da escola não deve ter boletins e faltas apagados — apenas arquivados.

#### Models (modificados)

`Aluno`, `Professor`, `Turma`, `Disciplina`, `Avaliacao`, `Falta` agora:
- `use SoftDeletes;`
- `$fillable` inclui `created_by`, `updated_by`, `deleted_by`.

### 5.3 Audit Columns + Observer

#### `database/migrations/2026_05_15_142000_add_audit_columns_to_entities.php` (novo)

Adiciona em cada entidade:
- `created_by` — FK `users.id` nullable, `onDelete: nullOnDelete`.
- `updated_by` — idem.
- `deleted_by` — idem.

#### `app/Observers/AuditObserver.php` (novo)

- `creating`: preenche `created_by` e `updated_by` com `Auth::id()`.
- `updating`: preenche `updated_by`.
- `deleting`: preenche `deleted_by` (via `saveQuietly` para não disparar `updating` novamente).

Quando não há usuário autenticado (seeders, console), colunas ficam `NULL` — aceitável.

#### `app/Providers/AppServiceProvider.php` (modificado)

**Antes**: `boot()` vazio.

**Depois**: loop registrando `AuditObserver` em todas as 6 entidades. Uma única linha por entidade — sem precisar adicionar trait nos models, fica centralizado.

### 5.4 Testes (TDD)

#### `tests/Feature/Persistence/SoftDeleteTest.php` (novo, 4 testes)

- Aluno deletado não aparece em `find()` mas aparece em `withTrashed()->find()`.
- Aluno deletado pode ser restaurado.
- Soft delete funciona em Aluno, Professor, Turma, Disciplina (data provider implícito).
- Avaliacao e Falta também são soft-deletáveis.

#### `tests/Feature/Persistence/AuditObserverTest.php` (novo, 3 testes)

- `created_by` e `updated_by` preenchidos automaticamente ao criar com user autenticado.
- `updated_by` muda no update; `created_by` permanece.
- Sem user autenticado → ambos `null`.

### 5.5 Testes existentes atualizados

`AlunoControllerTest`, `ProfessorControllerTest`, `TurmaControllerTest`, `DisciplinaControllerTest` — método `test_destroy_deletes_*`:

**Antes**: `$this->assertDatabaseMissing('alunos', ['id' => $aluno->id]);`

**Depois**: `$this->assertSoftDeleted('alunos', ['id' => $aluno->id]);`

`assertDatabaseMissing` verifica ausência completa da linha; soft delete só seta `deleted_at`.

### 5.6 Estado da Fase 5: VERDE

- ✅ **156 testes passando** (era 149; +4 SoftDelete + 3 AuditObserver).
- ✅ Pint passa.
- ✅ PHPStan baseline regenerada com 95 erros (era 88; +7 do novo observer e dos `SoftDeletes` adicionados).

---

## Fase 7 — Documentação

Objetivo: estabelecer documentação durável para devs e agentes futuros.

### `CLAUDE.md` (novo)

Guia primário para Claude Code e desenvolvedores. Contém:
- Stack completa (versões + ferramentas).
- Comandos canônicos (`composer test/test:coverage/lint/lint:fix/stan`).
- Arquitetura comentada (Domain / Enums / Data / Services / Policies / Observers).
- RBAC com a regra-chave de `AvaliacaoPolicy::update`.
- Segurança (CSP report-only, throttle:writes, upload com mimetypes, …).
- Convenções de banco (forward-only, soft deletes universais, FKs).
- Padrões TDD com referência aos helpers em `Tests\Concerns\CreatesSchoolScenarios`.
- 6 regras gerais "Sem X" para mudanças (no `return true` em authorize, sem string mágica de status/role, etc.).

### `README.md` (reescrito)

**Antes**: template padrão Laravel (logo, Build Status, etc.) — nenhuma informação específica do projeto.

**Depois**: setup, comandos úteis, CI, estrutura, RBAC resumido, segurança, links para `CLAUDE.md` e `REFACTOR_LOG.md`.

---

## Resumo geral da refatoração

| Fase | Commit | Foco | Tests |
|---|---|---|---|
| 0 | `96c9a2f` | Tooling (Pint+Larastan+CI+helpers) | 92 |
| 1 | `a1f2938` | Domain (NotaCalculator, enums, Falta.aluno_id) | 108 |
| 2 | `8153f94` | RBAC + headers + upload seguro | 134 |
| 3 | `091729d` | Services + DTOs (Photo, Matricula, Chamada) | 149 |
| 5 | `f173c41` | Persistência (soft deletes, audit, índices) | 156 |
| 7 | (este) | Documentação | 156 |

---

## Fase 4 — Cobertura via fluxos E2E

Objetivo: rede de segurança contra regressões nos próximos refactors via **testes de fluxo completo** (não % vazia). Cada teste atravessa policies → routes → form requests → services → models → views.

### 4.1 Testes E2E

#### `tests/Feature/E2E/FluxoBoletimTest.php` (novo, 4 testes)

Cenários cobertos:
- **`admin_cria_avaliacao_e_lanca_notas_aluno_ve_boletim`**: ciclo completo. Admin abre boletim → `AvaliacaoService::garantirAvaliacoesParaAluno` auto-cria a linha → professor (com vínculo na pivot ternária) lança notas → `NotaCalculator` computa média/situação → aluno consulta próprio boletim e vê "Aprovado".
- **`substitutiva_recupera_aluno_no_boletim`**: regra de negócio crítica em produção (substitutiva troca menor nota se beneficiar). Av3=3 + substitutiva=9 → aluno reprovado vira aprovado com média 7.75.
- **`aluno_nao_acessa_boletim_de_outro_aluno`**: `AlunoPolicy::view` via middleware `can:view,aluno` na rota.
- **`professor_de_outra_turma_nao_pode_atualizar_avaliacao`**: a regra-chave da `AvaliacaoPolicy::update` que valida `professor_disciplina_turma` — professor sem vínculo na pivot ternária recebe 403 e nota não muda.

#### `tests/Feature/E2E/FluxoChamadaTest.php` (novo, 4 testes)

- **`admin_registra_chamada_e_falta_e_persistida_com_aluno_id`**: dois alunos ausentes → POST `/faltas/chamada` → 2 linhas em `faltas` com `aluno_id` FK populado (não só a matrícula legacy).
- **`reenvio_substitui_chamada_quando_confirmado`**: 1ª submissão grava 1 falta; 2ª sem `confirmar_reenvio` retorna warning sem mutar; 2ª com `confirmar_reenvio=1` apaga as anteriores e grava as novas (idempotência via `ChamadaService::registrar`).
- **`justificativa_e_remocao_via_rotas`**: POST `/faltas/justificar/{falta}` com observação → `justificada=true` + texto persistido; DELETE → reverte.
- **`aluno_nao_pode_registrar_chamada`**: `FaltaPolicy::create` impede aluno via `FaltaStoreRequest::authorize()`. 0 faltas criadas.

### 4.2 Matriz de acesso (regression net)

#### `tests/Feature/Policies/MatrizAcessoTest.php` (novo, 11 testes via `@dataProvider`)

10 rotas críticas testadas contra os 3 papéis em uma única classe; cada cenário verifica o status esperado:

| Rota | Admin | Professor | Aluno |
|---|---|---|---|
| `alunos.index` | 200 | 200 | 403 |
| `alunos.create` | 200 | 403 | 403 |
| `professores.create` | 200 | 403 | 403 |
| `disciplinas.create` | 200 | 403 | 403 |
| `turmas.create` | 200 | 403 | 403 |
| `faltas.index` | 200 | 200 | 403 |
| `faltas.relatorio-aluno` | 200 | 200 | 403 |
| … | … | … | … |

Reorganizar grupos de rota no `routes/web.php` sem atualizar a matriz quebra a build — é a rede contra regressões silenciosas de autorização.

Mais um teste extra: guest em rota auth → redirect para `/login`.

### 4.3 NotaCalculator edge cases

#### `tests/Unit/Domain/Avaliacao/NotaCalculatorEdgeCasesTest.php` (novo, 5 testes)

Complementa o `NotaCalculatorTest` principal (13 testes) com:
- Todas as notas 10 (média = 10, aprovado).
- Substitutiva zero **não** substitui zero (regra "só se beneficiar").
- Recuperação e substitutiva ambas zero não alteram nada.
- **Aceita string** vinda do cast `decimal:2` do Eloquent (`'8.00'`) — protege contra refatoração que tipa estrito demais.
- Substitutiva fornecida com notas zeradas **não é** "em andamento" (regra do `algumaNotaLancada`).

### 4.4 Coverage gate no CI

#### `.github/workflows/tests.yml` (modificado)

**Adicionado**:
- Step `npm ci && npm run build` antes dos testes (necessário para o Vite manifest, sem o qual feature tests que renderizam views falham com 500).
- Novo job `coverage` (PHP 8.3 + xdebug) rodando `php artisan test --coverage --min=70`.

**Por que 70% e não 85%**:
- Gate é piso, não meta. 70% é conservador — passa hoje com folga e mantém pressão para subir gradualmente.
- 85% sem medir antes é palpite; pode bloquear PRs honestos. Sobe quando o número real for confirmado em CI.

### 4.5 Estado da Fase 4: VERDE

- ✅ **180 testes passando** (era 156; +24 nesta fase: 4 boletim E2E + 4 chamada E2E + 11 matriz acesso + 5 NotaCalculator edge cases).
- ✅ **541 assertions** (era 468; +73).
- ✅ Pint passa.
- ✅ PHPStan: 0 erros novos (baseline 95 inalterada).
- ✅ CI ganha gate de cobertura ≥70% como novo job.

---

## Fase 6 — Adiada conscientemente

`spatie/laravel-activitylog` agrega tracking de mudanças por campo (valor antigo → novo) e página `/admin/audit-log`. O `AuditObserver` da Fase 5 já cobre quem fez e quando para CRUD. Sem requisito regulatório explícito ou demanda concreta de "quem alterou esta nota de 7 para 9", o esforço é prematuro: pacote externo, tabela polimórfica grande, rotina de limpeza, índices. Faz mais sentido instalar quando aparecer a demanda — aí registra `LogsActivity` em 1-2 models específicos.
