# Decision 0002: Reuse Frontdemo02 Widgets

## Status

Accepted

## Context

Values01 needs a card visibility selector, runtime identity card, time card, Git status card, and system status card. Similar widgets already exist in `/tomaco3/htdocs/frontdemo02`.

## Decision

Reuse and adapt the following widgets from `frontdemo02`:

- Card Selector pattern from `customer-card-selector`
- Instance Info from `instance-info`
- Time from `local-time`
- Git Status from `git-status`
- System Status from `system-status`
- Variables CRUD from `variable-form` and `variable-list`

The Card Selector replaces `side01`.

The Instance Info widget replaces `top09`.

Time, Git Status, and System Status are added to the sidebar.

Variables CRUD is added as a separate widget at the bottom of the workbench.

## Consequences

- Existing behavior and visual structure can be reused instead of redesigned.
- The implementation should adapt naming and styling to Values01.
- Unrelated `frontdemo02` dependencies, customer-specific behavior, and customer-specific labels should not be copied unless explicitly specified.
- Controlled card ids for Values01 remain TBS until the final card registry is specified.
- System Status requires the `variables` table for runtime configuration values such as `vmware_cores`.
- Variables CRUD should follow Values01 shared CRUD behavior, including search, pagination, icon actions, and CSV import/export.
