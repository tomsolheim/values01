# Decision 0001: Use Livewire 4 and Bootstrap 5

## Status

Accepted

## Context

The project should support interactive server-driven interfaces while keeping the front-end stack approachable.

## Decision

Use Livewire 4 for interactive components and Bootstrap 5 for the base UI system.

## Consequences

- UI behavior can stay close to Laravel and Blade.
- Bootstrap provides a familiar component system without requiring a custom design system on day one.
- Livewire components should stay thin, with business logic pushed into actions or services.
