# ClasseUp

Sistema de gestão escolar (alunos, professores, turmas, disciplinas, chamada de presença, lançamento de notas com substitutiva e recuperação, boletim).

## Stack

- Laravel 12 / PHP 8.2+ (matriz CI: 8.2 / 8.3 / 8.4)
- Eloquent + SoftDeletes + audit columns automáticas
- RBAC nativo (3 papéis: Admin, Professor, Aluno) via Policies + enum
- TailwindCSS 3 + Alpine.js 3 + Vite 7
- PHPUnit 11.5, Larastan nível 6, Laravel Pint

## Setup

```bash
git clone <repo> && cd classeup
cp .env.example .env
composer install
npm install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build
composer dev   # roda servidor + queue + pail + vite
```

## Comandos úteis

```bash
composer test          # roda toda a suite (artisan test)
composer test:coverage # com cobertura
composer lint          # pint --test (dry-run)
composer lint:fix      # aplica pint
composer stan          # phpstan / larastan nível 6
```

## CI

`.github/workflows/tests.yml` roda em cada push e PR:
- Testes em matriz PHP 8.2 / 8.3 / 8.4
- `pint --test`
- `phpstan analyse`

## Estrutura

```
app/
├── Domain/      # Regras de negócio puras (NotaCalculator)
├── Enums/       # Type-safe (Role, SituacaoAvaliacao, …)
├── Data/        # DTOs imutáveis (readonly class)
├── Services/    # PhotoUpload, Matricula, Chamada, Avaliacao
├── Policies/    # Autorização granular por entidade
├── Observers/   # AuditObserver
└── Http/        # Controllers, Middleware, Form Requests
```

## RBAC

- **Admin**: acesso completo (CRUDs e relatórios).
- **Professor** (vinculado via `users.professor_id`): leitura administrativa, registra chamada e lança notas das próprias `(turma, disciplina)`.
- **Aluno** (vinculado via `users.aluno_id`): apenas próprio boletim e próprias faltas.

## Segurança

- Headers HTTP: CSP (report-only), X-Frame-Options DENY, HSTS em produção.
- Rate limiting (`throttle:writes`) em todas as rotas de mutação.
- Upload com validação de MIME real (`mimetypes`) e dimensões máximas.
- Soft delete preserva histórico escolar.
- Audit columns automáticas (`created_by`, `updated_by`, `deleted_by`).

## Documentação

- [`CLAUDE.md`](./CLAUDE.md) — guia para desenvolvedores e agentes (arquitetura, comandos, padrões).
- [`REFACTOR_LOG.md`](./REFACTOR_LOG.md) — histórico passo a passo da refatoração (com antes/depois).
