# Acceptance: Links Table and Tab08 Widget

## Criteria

- The application has a `links` table.
- The table includes `name`, `group`, `url`, `comment`, and timestamps.
- `name` and `url` are required.
- `group` and `comment` are optional.
- URL validation accepts `http://` and `https://` web addresses.
- Workbench `tab08` has the visible label `Links`.
- `tab08` shows the Links form/list widget rather than `info08`.
- The form uses labels `Name`, `Group`, `URL`, and `Comment`.
- The widget can create, list, edit, and delete links.
- Form show/hide affects only the form and leaves the list visible.
- The list shows `name`, `group`, `url`, and `comment`.
- Each valid URL is clickable and opens in a new browser tab.
- URL links use `target="_blank"` and `rel="noopener noreferrer"`.
- The list has a wide broad-search field.
- Broad search covers name, group, URL, and comment.
- The list has a Group filter populated with distinct non-empty stored groups.
- The Group filter defaults to `All groups`.
- Search and Group filter work together.
- The list has a Reset Search button.
- Reset Search clears broad search, selects `All groups`, and restores the unfiltered first page.
- Search, filtering, reset, pagination, and CRUD actions keep the Links tab active.
- The list uses Bootstrap 5 pagination.
- Edit and Delete use the shared compact icon-button presentation.
- The widget supports CSV import and export using `name`, `group`, `url`, and `comment`.

## Suggested Tests

- A migration test confirms the `links` table and expected columns.
- A validation test confirms name and URL are required.
- A validation test rejects unsupported or malformed URL schemes.
- A feature or Livewire test confirms links can be created, updated, and deleted.
- A browser test confirms clicking a URL opens a new tab safely.
- A feature or Livewire test confirms broad search covers all four list fields.
- A feature or Livewire test confirms Group filtering uses distinct stored groups.
- A feature or Livewire test confirms search and Group filter can be combined.
- A feature or Livewire test confirms Reset Search clears search and Group filter and resets pagination.
- A browser or Livewire test confirms Links interactions keep `tab08` active.
- A feature test confirms CSV import and export use the specified columns.
