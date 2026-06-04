# Spec-Driven Workflow

- Treat files in `specs/` as the product source of truth.
- Before implementing a feature, look for a matching feature spec and acceptance criteria.
- If a spec is missing or ambiguous, propose a concise spec update before writing broad code.
- Map acceptance criteria to tests in `tests/Feature` or `tests/Unit`.
- Keep Livewire components focused on UI state and delegate business behavior to actions or services.
