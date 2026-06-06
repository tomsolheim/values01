# Feature 009: Variables Table

## Goal

Define a variables table and a Variables CRUD widget for runtime configuration values used by reusable widgets such as System Status.

## User Story

As a project owner, I want configurable runtime values stored in a table so widgets can read local settings without hardcoding them.

## Data Model

### Variables Table

Table name: `variables`

Fields:

- `id`
- `name`
- `value`
- `group`
- `comment`
- `created_at`
- `updated_at`

## Field Definitions

`name`

- Variable key.
- Required.
- Short text.
- Example: `vmware_cores`.

`value`

- Variable value stored as text.
- Required.
- The consuming widget is responsible for interpreting the value type.

`group`

- Grouping label for related variables.
- Required.
- Short text.
- Examples: `system`, `runtime`, `ui`.

`comment`

- Human-readable description of the variable.
- Required.
- Text.

## Initial Variables

`vmware_cores`

- Group: `system`
- Purpose: number of host CPU cores reserved for VMware or other VM workloads.
- Used by System Status to calculate available CPU count.

`isin_counter`

- Group: `lookup`
- Purpose: remaining daily ISIN lookup count for the EODHD license quota.
- Initial value: `20`
- Used by Asset ISIN Lookup before calling the external provider.
- The value is decremented by `1` for each external ISIN lookup call.
- When the value reaches `0`, Asset ISIN Lookup must not call the external provider.
- Daily reset behavior is TBS and may be handled manually through the Variables CRUD widget until specified.

## Relationship to Widgets

- System Status reads variables from the `variables` table.
- Asset ISIN Lookup reads and updates `isin_counter` from the `variables` table.
- Other widgets may use variables later if specified.

## UI Placement

The Variables CRUD widget appears in a separate widget area at the bottom of the workbench.

Placement rules:

- The widget is below the main tabbed workbench content.
- The widget is visually part of the workbench column, not the sidebar.
- The widget should remain below the tabs regardless of which tab is active.
- The widget is not a workbench tab.
- The widget may be controlled by Card Selector if it is explicitly registered in the card registry.

## Variables CRUD Widget

The widget includes:

- A form area for creating and editing variables.
- A list area for viewing existing variables.
- A heading show/hide button that affects only the form area.
- An Update button in the title bar for manually refreshing the variables list.
- A search field for the list.
- Pagination for the list.
- Edit and Delete row actions.
- CSV import and CSV export controls.

The widget follows `specs/features/008-shared-crud-list-behavior.md`.

### Form

Form fields and labels:

| Label | Field | Control | Purpose |
| --- | --- | --- | --- |
| Name | `name` | Text input | Variable key. |
| Value | `value` | Text input | Variable value. |
| Group | `group` | Text input | Grouping label. |
| Comment | `comment` | Text input or textarea | Human-readable description. |

Expected controls:

- Save button
- Cancel or reset button when editing
- Show/hide form button in the widget heading
- Update button in the widget title bar

### Manual Refresh

- The Variables CRUD widget has an Update button in the title bar.
- The Update button manually refreshes the variables list.
- The Update button does not need to refresh automatically through polling or automatic Livewire updates at this stage.
- Pressing Update should preserve the current visible state of the widget where possible.
- Pressing Update should not hide or show the form by itself.
- Pressing Update should not reset the active workbench tab.

### List

Visible columns:

- `name`
- `value`
- `group`
- `comment`

Expected row actions:

- Edit
- Delete

Row action presentation follows `specs/features/008-shared-crud-list-behavior.md`.

## Scope

Included:

- Variables table specification
- Initial `vmware_cores` runtime variable
- Relationship to System Status
- Variables CRUD widget at the bottom of the workbench
- Shared CRUD list behavior

Not included:

- Type-specific validation
- Secret storage

## Open Questions

- Should `value` remain a string or support typed values?
- Should `comment` be required for all variables?
- Should the Variables widget be controlled by Card Selector?
