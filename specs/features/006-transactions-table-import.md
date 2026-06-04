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

The transaction import widget location is TBS.

## Scope

Included:

- Transactions table specification
- Source file mapping
- Import function specification
- ISIN-based import filter
- Relationship to Asset ISIN

Not included:

- Final workspace tab placement
- Reconciliation rules
- Calculation updates to holdings
- Import preview design
- Error reporting design
- Undo import
- Duplicate handling beyond unique `source_id`

## Open Questions

- Which workbench tab should contain the transaction import widget?
- Should import show a preview before saving?
- Should import support deleting or rolling back a full imported batch?
- Should transactions without ISIN be linked to an internal asset later?
