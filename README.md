# Values01

Values01 is a Laravel 13, Livewire 4, and Bootstrap 5 project being developed with a spec-driven workflow.

## Spec-Driven Process

Use the specs before changing code:

1. Write or update the feature spec in `specs/features/`.
2. Write or update acceptance criteria in `specs/acceptance/`.
3. Update progress in `specs/status/implementation-status.md`.
4. Implement the smallest useful version.
5. Verify with tests or manual checks.
6. Update status again.

Project instructions for coding agents are in `AGENTS.md`.

## Important Paths

- `specs/product/`: product vision and vocabulary
- `specs/features/`: intended feature behavior
- `specs/acceptance/`: testable criteria
- `specs/decisions/`: architecture decisions
- `specs/status/implementation-status.md`: implementation progress
- `app/Livewire/`: Livewire components
- `app/Actions/`: application actions
- `app/Services/`: shared services

## Local Development

Start Laravel on port 8005:

```bash
php artisan serve --host=127.0.0.1 --port=8005
```

Run tests:

```bash
php artisan test
```

When JavaScript tooling is available:

```bash
npm install
npm run dev
```
