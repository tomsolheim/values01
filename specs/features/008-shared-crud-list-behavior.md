# Feature 008: Shared CRUD List Behavior

## Goal

Define shared behavior and presentation rules for all CRUD list widgets.

## Applies To

This specification applies to every form/list widget that supports create, list, edit, and delete behavior.

Current CRUD widgets:

- Asset widget in `tab02`
- Bundle widget in `tab03`
- Area widget in `tab04`
- Holdings widget in `tab05`
- Links widget in `tab08`

Future CRUD widgets should follow this specification unless their own spec explicitly overrides it.

## List Behavior

- Lists are always visible.
- Lists include a search field.
- Lists use pagination.
- Form show/hide controls affect only the form area.
- Hiding a form must not hide the list.
- CRUD list interactions must preserve the active workbench tab.
- Pagination, search, edit, delete, save, cancel, and form show/hide actions must not reset the user to another tab.

## Import and Export

- Every CRUD widget has an import function.
- Every CRUD widget has an export function.
- Both import and export use CSV format.
- The import and export controls are shown as buttons.
- The buttons are placed side by side below the list.
- The button group is aligned to the right below the list.
- The buttons should be visually similar in size and style.
- The buttons should use clear labels, such as `Import CSV` and `Export CSV`.
- Import and export actions must preserve the active workbench tab.

## Pagination Presentation

- Pagination must use Bootstrap 5 pagination styling.
- Use Bootstrap's `pagination` and `page-item` / `page-link` classes.
- Prefer compact pagination using `pagination-sm` inside dense CRUD widgets.
- Pagination controls must fit visually inside the widget and must not create oversized arrows or icons.
- Previous and next controls should use concise text labels such as `Previous` and `Next`, or small accessible icons styled with Bootstrap sizing.
- If icon-only pagination controls are used, they must have accessible labels.
- Default framework pagination SVG arrows should not appear unstyled or oversized.
- Pagination should be placed below the list table unless a specific widget spec says otherwise.

## Row Actions

CRUD lists include row actions for:

- Edit
- Delete

## Action Button Presentation

- The Edit action is a small button with a pen icon.
- The Delete action is a small button with a garbage can icon.
- The Edit and Delete buttons are placed beside each other in the same row action area.
- The buttons should be visually similar in size and style.
- The buttons should use familiar iconography.
- The buttons should have accessible labels or tooltips so the icon meaning is clear.

## UI Notes

- Use Bootstrap 5 button styling.
- Prefer compact icon-only buttons in dense lists.
- Avoid text-only Edit/Delete buttons in CRUD lists unless a future spec explicitly requires them.
- Keep row actions visually aligned across all CRUD widgets.

## Scope

Included:

- Shared CRUD list visibility rules
- Shared search and pagination rules
- Bootstrap 5 pagination presentation rules
- Active tab persistence during CRUD interactions
- Shared Edit/Delete icon-button presentation
- Shared CSV import and export controls

Not included:

- Confirmation dialog behavior
- Bulk actions
- Sorting
- Authorization-specific button visibility
- Import validation details
- Export column ordering beyond each widget's visible/list fields
