# CLAUDE.md — Guia do projeto ClasseUp

Este documento orienta agentes (Claude Code, etc.) e desenvolvedores que entram no projeto. Sempre consulte antes de propor mudanças.

## Stack

- **Backend**: PHP 8.2+ (testado em 8.2, 8.3, 8.4) + Laravel 12
- **ORM**: Eloquent com `SoftDeletes` e `AuditObserver` em todas as entidades de domínio
- **Banco**: SQLite (dev/CI), suporte a MySQL/PostgreSQL/SQL Server
- **Auth**: Laravel Breeze (session-based) + RBAC nativo (Policies + enum `Role`)
- **Frontend**: Blade + TailwindCSS 3 + Alpine.js 3 + Vite 7
- **Testes**: PHPUnit 11.5 + Mockery; cenários via trait `Tests\Concerns\CreatesSchoolScenarios`
- **Lint**: Laravel Pint (preset `laravel` + `declare_strict_types`)
- **Análise estática**: Larastan nível 6 com baseline congelada em `phpstan-baseline.neon`
- **CI**: GitHub Actions com 3 jobs (tests matriz 8.2/8.3/8.4, lint, static-analysis)

## Comandos canônicos

```bash
composer install              # instala deps (use --ignore-platform-req=ext-bcmath em sandbox)
composer dev                  # serve + queue + pail + vite (concurrently)

composer test                 # roda artisan test
composer test:coverage        # roda com --coverage
composer lint                 # pint --test (dry-run, falha em diff)
composer lint:fix             # aplica formatação Pint
composer stan                 # phpstan analyse --memory-limit=1G
```

Para regenerar a baseline do PHPStan depois de refatoração significativa:
```bash
vendor/bin/phpstan analyse --memory-limit=1G --generate-baseline phpstan-baseline.neon
```

## Arquitetura

```
app/
├── Domain/           # Regras de negócio puras (sem deps Laravel)
│   └── Avaliacao/    # NotaCalculator + NotaFinal (Value Object)
├── Enums/            # Type-safe enums (Role, SituacaoAvaliacao, StatusMatricula, …)
├── Data/             # DTOs imutáveis (readonly class), com fromRequest()
├── Services/         # Orquestração HTTP-agnostic
│   ├── PhotoUploadService   # ULID filename + delete antigo
│   ├── MatriculaService     # geração de matrícula com lockForUpdate
│   ├── ChamadaService       # registro de presença idempotente
│   └── AvaliacaoService     # notas + boletim
├── Policies/         # Autorização granular (1 por entidade)
├── Observers/        # AuditObserver popula created_by/updated_by/deleted_by
├── Http/
│   ├── Controllers/  # ≤ 130 linhas; injetam services via construtor
│   ├── Middleware/   # SecurityHeaders, EnsureUserHasRole
│   └── Requests/     # FormRequest com authorize() real via Policy
└── Models/           # Eloquent + SoftDeletes + audit fillable
```

## RBAC (3 papéis)

- **Admin** (`Role::Admin`): gestão total. Pode CRUD de Aluno/Professor/Turma/Disciplina.
- **Professor** (`Role::Professor`, vinculado via `users.professor_id`): leitura administrativa + chamada e lançamento de notas das próprias `(turma, disciplina)`.
- **Aluno** (`Role::Aluno`, vinculado via `users.aluno_id`): apenas próprio boletim e próprias faltas.

A regra-chave (`App\Policies\AvaliacaoPolicy::update`): professor só atualiza avaliação se `(aluno.turma_id, disciplina_id, professor_id)` existir em `professor_disciplina_turma`.

## Segurança

- **Headers** (todos os responses web via `SecurityHeaders` middleware): `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy`, `Permissions-Policy`, **CSP em `report-only`** (não enforce até Alpine inline sair), `HSTS` em prod+https.
- **CSRF**: ativo por padrão (web group).
- **Rate limiting**: `throttle:writes` (60 req/min/user, 10/min/IP guest) em todas as rotas de mutação.
- **Upload**: além de `mimes`, validamos `mimetypes:image/jpeg,image/png,image/webp` (MIME real) e `dimensions:max_width=2000,max_height=2000`. Filename é ULID, não previsível.
- **CSP enforce**: pendente até retirarmos handlers inline de Alpine.

## Banco de dados

- Migrations são **forward-only**. Nunca edite uma migration versionada — crie uma nova.
- Todas as entidades de domínio (Aluno, Professor, Turma, Disciplina, Avaliacao, Falta) têm `SoftDeletes` e colunas `created_by/updated_by/deleted_by` (FK `users.id`).
- `faltas.aluno_id` é a FK canônica; `faltas.matricula` é soft-deprecated (drop planejado para uma fase futura).
- Pivot `professor_disciplina_turma` é a ternária canônica. A pivot `professor_disciplina` está marcada para consolidação.

## Padrões TDD

1. **RED**: teste falha antes da implementação. Sempre.
2. **GREEN**: implementação mínima para passar.
3. **REFACTOR**: limpa sem mudar comportamento — com testes verdes para garantir.

Unit tests vivem em `tests/Unit/` (sem booting do Laravel quando possível — `NotaCalculator` é puro). Feature tests vivem em `tests/Feature/`.

Use os helpers do `Tests\Concerns\CreatesSchoolScenarios`:
- `actingAsAdmin() / actingAsProfessor(?Professor) / actingAsAluno(?Aluno)`
- `createTurmaComProfessorDisciplina()` retorna `{turma, professor, disciplina}` linkados.

## Regras gerais para mudanças

- **Sem `return true` em `authorize()`**: sempre delegue para Policy.
- **Sem string mágica de status/role**: use o enum.
- **Sem `DB::table(...)->insert(...)` cru em controllers**: extraia para Service.
- **Sem `handleX()` privado duplicado entre controllers**: extraia para Service.
- **Sem strings de filename baseadas em `time().uniqid()`**: use `Str::ulid()` (não previsível).
- **Sem `assertDatabaseMissing` para destroy**: use `assertSoftDeleted` (todas as entidades são soft-delete).

## Histórico

Veja `REFACTOR_LOG.md` para o passo-a-passo de cada fase da refatoração, com antes/depois e justificativas.
