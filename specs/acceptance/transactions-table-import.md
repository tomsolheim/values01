# Acceptance: Transactions Table and Import

## Criteria

- The application has a `transactions` table.
- The `transactions` table includes `source_id`, date fields, transaction type, security name, ISIN, quantity, price, fees, currencies, amount, purchase value, result, balance, exchange rates, transaction text, and reference numbers.
- `source_id` is required.
- `source_id` is unique.
- `isin` is optional.
- The import function accepts the sample transaction export format.
- The import function supports importing all ISINs.
- The import function supports importing one selected or manually entered ISIN.
- The import function does not require ticker because the source file has no ticker column.
- The import function maps repeated `Valuta` columns to distinct currency fields.
- Transactions with ISIN can be matched to assets by `assets.isin`.
- Transactions without ISIN may still be imported.
- Imported decimal numbers using comma decimal separators are normalized for storage.

## Suggested Tests

- A migration test or database assertion confirms the `transactions` table exists with the expected columns.
- An import test confirms the sample file header is accepted.
- An import test confirms all rows can be imported.
- An import test confirms import can be filtered to one ISIN.
- An import test confirms rows for other ISINs are skipped when filtering by one ISIN.
- An import test confirms rows without ISIN can be imported in all-ISIN mode.
- An import test confirms duplicate `source_id` values are not imported twice.

## Open Acceptance Items

- Add acceptance criteria for import widget placement after its tab is specified.
- Add acceptance criteria for preview, rollback, and error reporting after those behaviors are specified.
