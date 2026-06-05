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
- ISIN lookup button beside the Save/Create button
- Cancel or reset button when editing

### ISIN Lookup

The asset form includes an ISIN lookup action for trying an external web service before saving the asset.

Initial placement:

- Place the lookup button beside the Save/Create button in the form action row.
- The button label should be concise, such as `Lookup ISIN`.
- The lookup button should be visually secondary to the Save/Create button.
- The lookup button should be available when the `isin` field has a value.

Expected behavior:

- The lookup uses the current `isin` field value.
- The lookup does not save the asset by itself.
- Before calling the external lookup provider, the lookup checks the `isin_counter` variable in the `variables` table.
- The `isin_counter` value represents the remaining daily ISIN lookups allowed by the current license.
- Every provider lookup attempt counts down `isin_counter` by `1`.
- The counter is decremented only when the application is about to call the external provider.
- If `isin_counter` is `0`, the application must not call the external provider.
- If `isin_counter` is `0`, show a popup-message with the exact text `todays lookup quota is used`.
- The lookup fills or suggests known asset fields returned by the service.
- Initial fields to fill when available:
  - `ticker`
  - `country`
  - `name`
  - `type`
  - `area_id`
- Existing manually entered values should not be overwritten silently.
- If a target field already contains a value, the UI should either keep the existing value or require clear user confirmation before replacing it.
- The user must still press Save/Create to persist the asset.
- Lookup errors should be shown as a small user-visible message inside the widget.
- Lookup errors should not break the page or clear the form.

Quota behavior:

- `isin_counter` is stored in the `variables` table.
- `isin_counter` is interpreted as an integer.
- If `isin_counter` is missing, empty, or not numeric, the lookup should fail softly and show a configuration-needed message rather than calling the provider.
- The daily reset process for `isin_counter` is TBS.
- Until reset behavior is specified, the counter is maintained manually through the Variables CRUD widget.

Preferred provider:

- Use EODHD Search API as the initial provider.
- Search endpoint:
  - `https://eodhd.com/api/search/{ISIN}?api_token={API_TOKEN}&fmt=json`
- Expected response fields to use:
  - `Code` -> `ticker`
  - `Country` -> `country` or Area mapping input
  - `Name` -> `name`
  - `Type` -> `type` mapping input
  - `ISIN` -> validation against requested `isin`
  - `Exchange` -> optional mapping input for area selection
  - `isPrimary` -> preferred-result selection

Fallback provider:

- OpenFIGI may be used as a fallback or later provider for identifier mapping.
- OpenFIGI request shape:
  - `POST https://api.openfigi.com/v3/mapping`
  - Body includes `idType = ID_ISIN` and `idValue = {ISIN}`.
- OpenFIGI fields may be used for ticker, name, exchange code, market sector, and security type.

Result selection:

- ISIN lookup may return multiple listings.
- Prefer a result where `isPrimary = true` when the provider supplies it.
- If there is no primary result, prefer a result matching the current `country` field when that field is set.
- If multiple plausible results remain, the user should be shown a compact choice list instead of the application choosing silently.
- If no result is found, show a clear "No match found" message and leave the form unchanged.

Local field mapping:

- External asset type should map into the local Type dropdown:
  - Common stock or stock -> `Stock`
  - Bank, if identifiable -> `Bank`
  - ETF, fund, mutual fund -> `Fund`
  - Anything else -> `Other`
- External country should map into the local Country dropdown when possible:
  - `Norway` or `NOR` -> `NO`
  - `Sweden` or `SWE` -> `SE`
  - `Denmark` or `DNK` -> `DK`
  - `Germany` or `DEU` -> `DE`
  - `France` or `FRA` -> `F`
  - `Spain` or `ESP` -> `ES`
  - `United States`, `USA`, or `US` -> `US`
  - `United Kingdom`, `GBR`, or `UK` -> `UK`
  - Unknown or unsupported -> `Other`
- Area mapping is local and should be based on existing `areas` records.
- If an Area matching the mapped country code exists, select it.
- If no matching Area exists, select Area `Unknown` when available.
- The lookup should not create new Area records in this first implementation.

Configuration:

- Store API credentials in environment/config, not in source code.
- Suggested environment key: `EODHD_API_TOKEN`.
- The project `.env.example` should include `EODHD_API_TOKEN=` as an empty placeholder.
- Setup notes should explain that the user must obtain an EODHD API token and add it to `.env`.
- Setup notes should mention running `php artisan config:clear` or restarting the app after changing the token.
- Real API tokens must not be committed to Git.
- If no API token is configured, the lookup button may be disabled or may show a clear configuration-needed message.
- The application should use a small service class for provider calls so the UI component does not contain provider-specific HTTP details.

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
- ISIN lookup button in the asset form
- Initial EODHD-based lookup behavior
- Local mapping from lookup result to asset form fields
- Daily lookup quota counter using `variables.isin_counter`
- Pagination for the asset list
- Show/hide form button in the widget heading
- Shared CRUD list behavior from `008-shared-crud-list-behavior`

Not included:

- Import/export
- Automatic asset saving from lookup
- Automatic Area creation from lookup
- Live price lookup
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
