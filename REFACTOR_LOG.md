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

Próximas etapas (Fase 1):
- Extrair `NotaCalculator` para `app/Domain/Avaliacao/` (com testes unitários puros).
- Criar enums `SituacaoAvaliacao`, `NivelEducacional`, `Turno`, `StatusMatricula`, `Status`.
- Migration `add_aluno_id_to_faltas_table` corrigindo o relacionamento (de string `matricula` para FK).
- Migration `normalize_status_columns` (`ativo` boolean → `status` string em Professor/Turma/Disciplina).
