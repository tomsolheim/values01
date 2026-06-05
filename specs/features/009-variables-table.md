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

## Relationship to Widgets

- System Status reads variables from the `variables` table.
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
