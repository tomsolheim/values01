# Acceptance: Shared CRUD List Behavior

## Criteria

- Every CRUD list remains visible when its form is hidden.
- Every CRUD list has a search field.
- Every CRUD list is paginated.
- Every CRUD list interaction preserves the active workbench tab.
- CRUD pagination does not reset the user to `tab01`.
- Every CRUD widget has an import button.
- Every CRUD widget has an export button.
- Import and export use CSV format.
- Import and export buttons are side by side below the list.
- Import and export buttons are aligned to the right below the list.
- Import and export actions preserve the active workbench tab.
- Every CRUD list uses Bootstrap 5 pagination styling.
- Pagination uses compact controls suitable for the widget.
- Pagination controls do not show oversized default arrows or icons.
- Previous and next controls have accessible labels.
- Every CRUD list shows Edit as a small pen-icon button.
- Every CRUD list shows Delete as a small garbage-can-icon button.
- Edit and Delete buttons are beside each other.
- Edit and Delete buttons have accessible labels or tooltips.
- Edit and Delete buttons are visually similar in size and style.

## Suggested Tests

- A feature or browser test confirms hiding a form does not hide its list.
- A feature or browser test confirms each CRUD list has a search field.
- A feature or browser test confirms each CRUD list has pagination controls.
- A browser or Livewire test confirms CRUD pagination preserves the active tab.
- A feature or browser test confirms each CRUD widget has side-by-side CSV import and export buttons below the list.
- A browser or Livewire test confirms CSV import and export actions preserve the active tab.
- A browser or visual test confirms pagination uses Bootstrap 5 styling and does not render oversized arrows.
- A feature or browser test confirms each CRUD list has icon-only Edit and Delete buttons with accessible names.
