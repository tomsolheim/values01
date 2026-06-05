# Feature 007: History Table and Tab06 List

## Goal

Define a history table and a list widget in `tab06` for reviewing asset transaction history by ticker or ISIN.

## User Story

As a user, I want to view historical asset activity filtered by ticker or ISIN so I can inspect the transaction path for one asset.

## Data Model

### History Table

Table name: `history`

Fields:

- `id`
- `isin`
- `tic`
- `date`
- `type`
- `count`
- `price`
- `rate`
- `value`
- `cost`
- `acc`
- `total`
- `created_at`
- `updated_at`

## Field Definitions

| Label | Field | Purpose |
| --- | --- | --- |
| ISIN | `isin` | ISIN number. |
| TIC | `tic` | Ticker symbol. |
| Date | `date` | Transaction date. |
| Type | `type` | Transaction type. |
| Count | `count` | Count of assets in transaction. |
| Price | `price` | Share price. |
| Rate | `rate` | Exchange rate. |
| Value | `value` | Value of assets in transaction. |
| Cost | `cost` | Cost of transaction. |
| Acc | `acc` | Accumulated count. |
| Total | `total` | Total value. |

## Validation Rules

- `isin` is optional.
- `tic` is optional.
- At least one of `isin` or `tic` should be present when the row represents an asset.
- `date` is optional until import/calculation rules are finalized.
- Numeric fields should allow decimal values.
- Empty optional fields may be stored as `null`.

## UI Placement

The history list widget belongs in the workbench tab named `tab06`.

In the first implementation step:

- `tab06` no longer only shows `info06`.
- `tab06` shows the history list widget.

## Widget Behavior

### Selector

The widget includes a selector for choosing how to filter the history list.

Selector options:

- `Ticker`
- `ISIN`

Expected behavior:

- When `Ticker` is selected, the filter input searches or selects by `tic`.
- When `ISIN` is selected, the filter input searches or selects by `isin`.
- The selected filter value controls which history rows are shown.
- If no filter value is selected or entered, the list may show no rows or all rows. This behavior is TBS.

### List

The widget includes a list or table of history rows.
The list uses pagination.
The list includes a search field.

Visible columns:

- `isin`
- `tic`
- `date`
- `type`
- `count`
- `price`
- `rate`
- `value`
- `cost`
- `acc`
- `total`

## Relationship to Other Tables

- `history.isin` may match `assets.isin`.
- `history.tic` may match `assets.ticker`.
- History rows may later be generated from imported transactions, but generation rules are not yet specified.

## Scope

Included:

- History table specification
- Field labels and purposes
- History list widget in `tab06`
- Selector for filtering by ticker or ISIN
- Pagination for the history list
- Search field for the history list

Not included:

- Form for manual history entry
- Generation rules from transactions
- Calculation formulas
- Sorting rules
- Export

## Open Questions

- Should `history` rows be manually entered, generated from transactions, or both?
- Should an empty selector value show all rows or no rows?
- Should the selector be a dropdown, segmented control, or radio control?
- Should the filter value be selected from existing assets or typed manually?
