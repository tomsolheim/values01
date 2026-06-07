# Feature 010: Links Table and Tab08 Widget

## Goal

Define a Links table and CRUD form/list widget in workbench `tab08`.

## User Story

As a user, I want to maintain grouped web links so useful external resources can be searched, filtered, and opened directly from the application.

## Data Model

### Links Table

Table name: `links`

Fields:

- `id`
- `name`
- `group`
- `url`
- `comment`
- `created_at`
- `updated_at`

## Field Definitions

`name`

- Human-readable link name.
- Required.
- Short text.

`group`

- Optional grouping label.
- Short text.
- Used by the Group filter in the Links list.

`url`

- Web address opened by the link.
- Required.
- Text.
- Must be a valid `http://` or `https://` URL.

`comment`

- Optional free-form note.
- Text.

## UI Placement

- The widget belongs in workbench `tab08`.
- The visible tab label is `Links`.
- `tab08` no longer shows the `info08` placeholder.
- Interactions in the Links widget must keep the user on the Links tab.

## Form

The Links widget includes a form for creating and editing links.

Form fields:

| Label | Field | Control | Content |
| --- | --- | --- | --- |
| Name | `name` | Text input | Free manual content |
| Group | `group` | Text input | Free manual content |
| URL | `url` | URL or text input | Valid `http://` or `https://` URL |
| Comment | `comment` | Textarea | Free manual content |

Form behavior:

- The widget heading includes a show/hide button for the form.
- Show/hide affects only the form; the list remains visible.
- The form has a Save/Create button.
- The form has a Cancel or reset button when editing.
- Validation errors are shown beside or near the relevant fields.

## List

The list shows:

- `name`
- `group`
- `url`
- `comment`

URL behavior:

- The displayed URL is a clickable link.
- Clicking the URL opens it in a new browser tab.
- The link uses `target="_blank"`.
- The link uses `rel="noopener noreferrer"` for safe new-tab behavior.
- Long URLs should wrap or truncate cleanly without breaking the table layout.

### Search And Filter

- The list has a wide broad-search field.
- Broad search covers `name`, `group`, `url`, and `comment`.
- The list has a Group filter dropdown.
- Group filter options are the distinct non-empty `group` values stored in the `links` table.
- The default Group filter option is `All groups`.
- Broad search and Group filter may be used together.
- A record must match both the search text and selected Group when both are active.
- Changing search or Group resets pagination to the first page.

Reset behavior:

- Add a Reset Search button beside the search and Group filter controls.
- Reset Search clears the broad-search field.
- Reset Search returns the Group filter to `All groups`.
- Reset Search restores the unfiltered first page.
- Search, filtering, and reset actions keep the Links tab active.

### Pagination And Actions

- The list uses Bootstrap 5 compact pagination.
- The list is always visible.
- Row actions are Edit and Delete.
- Edit uses a small pen-icon button.
- Delete uses a small garbage-can-icon button.
- Row actions follow `specs/features/008-shared-crud-list-behavior.md`.

### CSV Import And Export

- The widget includes CSV Import and Export buttons.
- The buttons appear side by side below the list and are aligned to the right.
- CSV columns are `name`, `group`, `url`, and `comment`.
- Import and export follow the shared CRUD behavior.

## Scope

Included:

- `links` table
- Links CRUD form/list widget
- Placement in `tab08` with label `Links`
- Wide broad search
- Group filter
- Reset Search button
- Clickable URL opening in a new browser tab
- Pagination
- Shared CRUD icon actions
- CSV import and export

Not included:

- Automatic link metadata or favicon lookup
- Link availability checking
- Authentication or per-user links
- Sorting or drag-and-drop ordering

## Edge Cases

- Empty Group values remain allowed and appear when `All groups` is selected.
- Duplicate URLs are allowed unless a later requirement specifies uniqueness.
- Invalid or unsupported URL schemes must not be saved.
- A malformed URL must never be rendered as an active link.
