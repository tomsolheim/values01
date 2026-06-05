# Acceptance: Variables Table

## Criteria

- The application has a `variables` table.
- The `variables` table includes `name`, `value`, `group`, `comment`, and timestamps.
- `name` is required.
- `value` is required.
- `group` is required.
- `comment` is required.
- System Status can read `vmware_cores` from the `variables` table when available.
- If `vmware_cores` is missing, System Status should fall back safely.
- The Variables CRUD widget appears at the bottom of the workbench.
- The Variables CRUD widget is below the main tabbed workbench content.
- The Variables CRUD widget is not a workbench tab.
- The Variables form uses the labels `Name`, `Value`, `Group`, and `Comment`.
- The Variables widget can create a variable.
- The Variables widget can list existing variables.
- The Variables widget can edit an existing variable.
- The Variables widget can delete a variable.
- The Variables list shows the columns `name`, `value`, `group`, and `comment`.
- The Variables list follows the shared CRUD list behavior.
- The Variables widget supports CSV import and CSV export.

## Suggested Tests

- A migration test or database assertion confirms the `variables` table exists with the expected columns.
- A validation test confirms required fields are enforced.
- A feature or Livewire test confirms System Status can read `vmware_cores`.
- A feature or Livewire test confirms System Status falls back safely when `vmware_cores` is missing.
- A feature or browser test confirms the Variables widget appears below the tabbed workbench.
- A feature or Livewire test confirms variables can be created, listed, updated, and deleted.
- A feature or browser test confirms the Variables list follows shared CRUD behavior.
