# Agent Instructions

This project is spec-driven. The specs describe intended behavior; the implementation status describes what currently exists in code.

## Source of Truth

- Product intent lives in `specs/product/`.
- Feature behavior lives in `specs/features/`.
- Testable acceptance criteria live in `specs/acceptance/`.
- Architecture decisions live in `specs/decisions/`.
- Current progress lives in `specs/status/implementation-status.md`.

Do not treat implementation status as a replacement for the feature specs. Status is only a progress register.

## Required Workflow

Before implementing a change:

1. Find the matching feature spec in `specs/features/`.
2. Find the matching acceptance criteria in `specs/acceptance/`.
3. If the behavior is missing or unclear, update the spec first.
4. Update `specs/status/implementation-status.md` from `Specified` to `Partially implemented` when implementation begins.
5. Add or update tests that match the acceptance criteria.
6. Implement the smallest useful version.
7. Run the relevant tests.
8. Mark the status as `Implemented` only after code exists.
9. Mark the status as `Verified` only after tests or manual verification prove it works.

## Status Values

Use these exact values:

- `Not started`: no useful spec or implementation exists yet.
- `Specified`: the intended behavior is documented.
- `Partially implemented`: some code exists, but acceptance criteria are not complete.
- `Implemented`: code exists for the specified behavior.
- `Verified`: implementation has been tested or manually verified.
- `Deferred`: intentionally postponed.
- `Blocked`: cannot proceed until a named issue is resolved.

## Implementation Rules

- Keep Livewire components focused on UI state and interaction.
- Put business behavior in `app/Actions` or `app/Services` when it grows beyond simple UI logic.
- Use Bootstrap 5 for UI controls and layout.
- Keep stable tab ids (`tab01` through `tab10`) separate from visible tab labels.
- Preserve the spec vocabulary in labels, field names, and tests unless a spec update changes it.
- Do not invent calculation formulas for financial fields before they are specified.
- Do not implement transaction import behavior beyond the current spec without updating the import spec first.

## Git Practice

Prefer small commits:

- Spec updates first.
- Implementation second.
- Test or verification updates with the related implementation.

Good examples:

- `Add holdings table spec`
- `Implement asset form list widget`
- `Verify history tab filtering`
