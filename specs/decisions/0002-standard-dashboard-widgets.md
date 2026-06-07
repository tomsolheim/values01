# Decision 0002: Standard Dashboard Widgets

## Status

Accepted

## Context

Values01 needs standard dashboard widgets for identity, card visibility, time, Git status, system status, and runtime variables.

## Decision

Define these widgets as Values01 components:

- Identity card in `top01`
- Instance Info in `top09`
- Card Selector in `side01`
- Time in the sidebar
- Git Status in the sidebar
- System Status in the sidebar
- Variables CRUD in workbench `tab09`, labelled `Variables`

## Consequences

- The Values01 specs stand on their own and describe required behavior directly.
- Implementation may adapt existing code where useful, but external project references do not belong in product specs.
- System Status requires the `variables` table for runtime configuration values such as `vmware_cores`.
- Variables CRUD follows Values01 shared CRUD behavior, including search, pagination, icon actions, and CSV import/export.
