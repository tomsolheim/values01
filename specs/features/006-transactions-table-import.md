# Feature 006: Transactions Table and Import

## Goal

Define a transactions table and an import function for transaction files that identify securities by ISIN rather than ticker.

## User Story

As a user, I want to import transactions from a broker export so transaction history can be stored and later connected to assets and holdings.

## Sample File Shape

The supplied sample file is a tab-separated export encoded as UTF-16.

Observed source columns:

- `Id`
- `Bokføringsdag`
- `Handelsdag`
- `Oppgjørsdag`
- `Portefølje`
- `Transaksjonstype`
- `Verdipapir`
- `ISIN`
- `Antall`
- `Kurs`
- `Rente`
- `Totale Avgifter`
- `Valuta`
- `Beløp`
- `Valuta`
- `Kjøpsverdi`
- `Valuta`
- `Resultat`
- `Valuta`
- `Totalt antall`
- `Saldo`
- `Vekslingskurs`
- `Transaksjonstekst`
- `Makuleringsdato`
- `Sluttseddelnummer`
- `Verifikationsnummer`
- `Kurtasje`
- `Valuta`
- `Valutakurs`
- `Innledende rente`

The export has repeated `Valuta` columns. The import specification must map each repeated source column to a distinct transaction field.

## Data Model

### Transactions Table

Table name: `transactions`

Fields:

- `id`
- `source_id`
- `booked_date`
- `trade_date`
- `settlement_date`
- `portfolio`
- `transaction_type`
- `security_name`
- `isin`
- `quantity`
- `price`
- `interest`
- `total_fees`
- `fees_currency`
- `amount`
- `amount_currency`
- `purchase_value`
- `purchase_value_currency`
- `result`
- `result_currency`
- `total_quantity`
- `balance`
- `exchange_rate`
- `transaction_text`
- `cancellation_date`
- `contract_note_number`
- `verification_number`
- `brokerage`
- `brokerage_currency`
- `currency_rate`
- `initial_interest`
- `created_at`
- `updated_at`

## Source Mapping

| Source Column | Field |
| --- | --- |
| `Id` | `source_id` |
| `Bokføringsdag` | `booked_date` |
| `Handelsdag` | `trade_date` |
| `Oppgjørsdag` | `settlement_date` |
| `Portefølje` | `portfolio` |
| `Transaksjonstype` | `transaction_type` |
| `Verdipapir` | `security_name` |
| `ISIN` | `isin` |
| `Antall` | `quantity` |
| `Kurs` | `price` |
| `Rente` | `interest` |
| `Totale Avgifter` | `total_fees` |
| First `Valuta`, after `Totale Avgifter` | `fees_currency` |
| `Beløp` | `amount` |
| Second `Valuta`, after `Beløp` | `amount_currency` |
| `Kjøpsverdi` | `purchase_value` |
| Third `Valuta`, after `Kjøpsverdi` | `purchase_value_currency` |
| `Resultat` | `result` |
| Fourth `Valuta`, after `Resultat` | `result_currency` |
| `Totalt antall` | `total_quantity` |
| `Saldo` | `balance` |
| `Vekslingskurs` | `exchange_rate` |
| `Transaksjonstekst` | `transaction_text` |
| `Makuleringsdato` | `cancellation_date` |
| `Sluttseddelnummer` | `contract_note_number` |
| `Verifikationsnummer` | `verification_number` |
| `Kurtasje` | `brokerage` |
| Fifth `Valuta`, after `Kurtasje` | `brokerage_currency` |
| `Valutakurs` | `currency_rate` |
| `Innledende rente` | `initial_interest` |

## Import Function

The import function accepts the transaction export file and imports rows into the `transactions` table.

Import modes:

- All ISINs
- One ISIN

The user may choose:

- Import all rows from the file.
- Import only rows where `ISIN` matches a selected or manually entered ISIN.

Because the file has no ticker column, ticker-based filtering should not be used for this import. The filter label may explain this as "ISIN filter".

## Asset Creation From Import

The import function includes an option to add assets to the `assets` table from the imported transaction file.

Expected behavior:

- The function creates only new assets.
- Existing assets are checked by name before creating a new asset.
- If an asset with the same name already exists, the importer does not create a duplicate asset.
- Asset creation is independent of transaction duplicate handling.
- If a transaction row is skipped because its `source_id` already exists, the same row may still be used to create a missing asset when the add-assets option is enabled.
- A repeated import with add-assets enabled must be able to create missing assets even when all matching transactions have already been imported.
- The ISIN filter still applies before asset creation. In One ISIN mode, only rows matching the selected ISIN may create assets.
- The importer includes all known asset data from the transaction file.
- Known asset data from the transaction file includes:
  - `ISIN`
  - Company/security name from `Verdipapir`
- Imported assets use `type = Stock`.
- Imported assets use Bundle `Import`.
- Imported assets use Area `Unknown`.
- If Bundle `Import` does not exist, the importer may create it.
- If Area `Unknown` does not exist, the importer may create it.
- Rows without company/security name should not create an asset.
- Rows without ISIN may create an asset only if there is enough information to avoid duplicates. This behavior is TBS.

Field mapping for imported assets:

| Asset Field | Import Source / Value |
| --- | --- |
| `type` | `Stock` |
| `isin` | Source column `ISIN` |
| `name` | Source column `Verdipapir` |
| `bundle_id` | Bundle named `Import` |
| `area_id` | Area named `Unknown` |

## Relationship to Assets

- Transactions should match assets by `transactions.isin = assets.isin`.
- The Asset table, form, and list include `isin`.
- Rows without an ISIN may still be imported if they represent cash movements, fees, premiums, or other non-security transactions.
- Rows without an ISIN cannot be matched directly to an asset.

## Validation Rules

- `source_id` is required.
- `source_id` should be unique to prevent duplicate imports from the same export.
- Date fields are optional dates.
- `isin` is optional because some transaction rows have no security.
- Numeric fields should allow decimal values.
- Imported decimal values may use comma as decimal separator and should be normalized before storage.
- Empty source cells may be stored as `null`.

## UI Placement

The transaction import widget belongs in the workbench tab named `tab07`.

The visible tab label is `Import`.

In the first implementation step:

- `tab07` no longer only shows `info07`.
- `tab07` shows the transaction import widget.

## Transaction Table Maintenance

The Import tab includes a compact transaction table status and maintenance area.

Record count:

- Show the current number of records in the `transactions` table.
- The count should be clearly labelled, for example `Transactions: 116`.
- The displayed count is not required to update automatically through Livewire polling at this stage.
- Add a Refresh button beside the displayed count.
- Pressing Refresh reads the current transaction count from the database.
- Pressing Refresh must keep the user on the Import tab.
- A successful transaction import should update the displayed count.

Delete all:

- Add a `Delete all transactions` button.
- The button deletes every record from the `transactions` table.
- The action affects only transaction records; it must not delete Assets, Bundles, Areas, Variables, or imported source files.
- The action is destructive and must require explicit confirmation before deletion.
- The confirmation must clearly state that all transaction records will be permanently deleted.
- Cancelling confirmation must leave all transaction data unchanged.
- After successful deletion, the displayed transaction count must show `0`.
- After deletion, show a clear success message.
- Pressing or cancelling the action must keep the user on the Import tab.

## Scope

Included:

- Transactions table specification
- Source file mapping
- Import function specification
- ISIN-based import filter
- Relationship to Asset ISIN
- Asset creation from import
- Transaction import widget in `tab07`
- Transaction record count with manual Refresh button
- Confirmed delete-all-transactions action

Not included:

- Reconciliation rules
- Calculation updates to holdings
- Import preview design
- Error reporting design
- Undo import
- Undo delete-all-transactions
- Duplicate transaction handling beyond unique `source_id`

## Open Questions

- Should import show a preview before saving?
- Should import support deleting or rolling back one imported batch?
- Should transactions without ISIN be linked to an internal asset later?
- Should the add-assets function be a separate button or an option during transaction import?
- Should duplicate asset checking use only name, or later include ISIN as an additional duplicate guard?
