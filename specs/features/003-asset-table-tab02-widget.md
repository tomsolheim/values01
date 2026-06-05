# Feature 003: Asset Table and Tab02 Widget

## Goal

Define an asset table and a form/list widget that appears in the workbench under `tab02`.

## User Story

As a user, I want to maintain a list of assets so I can describe, categorize, and review assets in one structured place.

## Data Model

### Asset Table

Table name: `assets`

Fields:

- `id`
- `type`
- `isin`
- `ticker`
- `country`
- `name`
- `bundle_id`
- `area_id`
- `comment`
- `created_at`
- `updated_at`

### Field Definitions

`type`

- Asset classification.
- Required.
- Short text.
- Examples: `stock`, `fund`, `bond`, `cash`, `crypto`, `real_estate`, `other`.

`isin`

- International Securities Identification Number.
- Optional.
- Short text.
- Used to match imported transactions to assets when the import file has no ticker.
- Should be unique when supplied.

`ticker`

- Market ticker, symbol, or short asset code.
- Optional.
- Short text.
- Should be stored uppercase when relevant.

`country`

- Country or market region.
- Optional.
- Short text.
- Examples: `US`, `NO`, `SE`, `Global`.

`name`

- Human-readable asset name.
- Required.
- Text.

`bundle_id`

- Reference to the selected bundle.
- Optional.
- Foreign key to `bundles.id`.
- The form should display bundle names, not raw ids.

`area_id`

- Reference to the selected area.
- Optional.
- Foreign key to `areas.id`.
- The form should display area names, not raw ids.

`comment`

- Free-form note about the asset.
- Optional.
- Long text.

## Validation Rules

- `type` is required.
- `name` is required.
- `isin`, `ticker`, and `country` are optional short strings.
- `isin` should be unique when supplied.
- `bundle_id` is optional and must refer to an existing bundle when supplied.
- `area_id` is optional and must refer to an existing area when supplied.
- `comment` is optional long text.
- Empty optional fields may be stored as `null`.

## UI Placement

The asset form/list widget belongs in the workbench tab named `tab02`.

In the first implementation step:

- `tab02` no longer only shows `info02`.
- `tab02` shows the asset widget.
- Other tabs may remain placeholder tabs.

## Widget Behavior

### Heading

The widget heading includes a show/hide button for the form.

Expected behavior:

- The button toggles the form visibility.
- The list remains visible when the form is hidden.
- The show/hide behavior affects only the form area.
- The button label should clearly indicate the next action, such as `Show form` or `Hide form`.

### Form

The widget includes a form for creating or editing an asset.

Form fields and labels:

| Label | Field | Control | Values / Source |
| --- | --- | --- | --- |
| Type | `type` | Dropdown | `Stock`, `Bank`, `Fund`, `Other` |
| ISIN | `isin` | Text input | Free manual content |
| Tic | `ticker` | Text input | Free manual content |
| Country | `country` | Dropdown | `NO`, `SE`, `DK`, `DE`, `F`, `ES`, `US`, `UK`, `Other` |
| Name | `name` | Text input | Free manual content |
| Bundle | `bundle_id` | Dropdown | Options from the `bundles` table |
| Area | `area_id` | Dropdown | Options from the `areas` table |
| Comment | `comment` | Textarea | Free manual content |

Expected controls:

- Dropdown for `type`
- Text input for `isin`
- Text input for `ticker`
- Dropdown for `country`
- Text input for `name`
- Dropdown for `bundle`
- Dropdown for `area`
- Textarea for `comment`
- Save button
- Cancel or reset button when editing

### List

The widget includes a list or table of existing assets.
The list uses pagination.
The list includes a search field.

Visible columns:

- `type`
- `isin`
- `ticker`
- `country`
- `name`
- `bundle`
- `area`
- `comment`

Expected row actions:

- Edit
- Delete

Row action presentation follows `specs/features/008-shared-crud-list-behavior.md`.

## Scope

Included:

- Asset data table specification
- Form/list widget specification
- Connection between the widget and `tab02`
- Create, list, edit, and delete behavior
- Pagination for the asset list
- Show/hide form button in the widget heading
- Shared CRUD list behavior from `008-shared-crud-list-behavior`

Not included:

- Import/export
- External market data lookup
- Ticker validation against live exchanges
- Advanced filtering
- Authorization rules

## UI Notes

- Use Bootstrap 5 form controls and table styling.
- Use the specified labels exactly in the asset form.
- Keep the widget compact enough to live inside the workbench.
- The list should be scannable.
- The form and list may be stacked vertically in the first implementation.

## Edge Cases

- Assets without tickers should still be allowed.
- Long comments should not break the table layout.
- Deleting an asset should require a clear user action.
- Editing an existing asset should not create a duplicate row.
