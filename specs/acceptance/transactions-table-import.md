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
- `tab07` in the workbench shows the transaction import widget.
- The visible label for `tab07` is `Import`.
- Transactions with ISIN can be matched to assets by `assets.isin`.
- Transactions without ISIN may still be imported.
- Imported decimal numbers using comma decimal separators are normalized for storage.
- The import widget includes a function to add assets to the Asset table.
- The add-assets function creates only new assets.
- Existing assets are checked by name before creating a new asset.
- Asset creation is independent of duplicate transaction skipping.
- A repeated import with add-assets enabled can create missing assets even when the matching transactions already exist.
- In One ISIN mode, add-assets only creates assets from rows matching the selected ISIN.
- Imported assets include ISIN from the source `ISIN` column when available.
- Imported assets include company/security name from source `Verdipapir`.
- Imported assets use `type = Stock`.
- Imported assets use Bundle `Import`.
- Imported assets use Area `Unknown`.
- Rows without company/security name do not create assets.
- The Import tab shows the current number of records in the `transactions` table.
- The Import tab has a Refresh button beside the transaction count.
- Pressing Refresh updates the count from the database and keeps the Import tab active.
- A successful import updates the displayed transaction count.
- The Import tab has a `Delete all transactions` button.
- Delete all requires explicit confirmation that all transaction records will be permanently deleted.
- Cancelling deletion leaves transaction data unchanged.
- Confirming deletion removes all records from `transactions` only.
- Delete all does not remove Assets, Bundles, Areas, Variables, or source files.
- After deletion, the displayed transaction count is `0` and a success message is shown.
- The transaction count does not require automatic Livewire polling at this stage.

## Suggested Tests

- A migration test or database assertion confirms the `transactions` table exists with the expected columns.
- An import test confirms the sample file header is accepted.
- An import test confirms all rows can be imported.
- An import test confirms import can be filtered to one ISIN.
- An import test confirms rows for other ISINs are skipped when filtering by one ISIN.
- An import test confirms rows without ISIN can be imported in all-ISIN mode.
- An import test confirms duplicate `source_id` values are not imported twice.
- A feature or Livewire test confirms the transaction import widget is available in `tab07`.
- A feature or Livewire test confirms the add-assets function creates new assets from imported transaction rows.
- A feature or Livewire test confirms the add-assets function can create missing assets from rows whose transactions were skipped as duplicate `source_id` values.
- A feature or Livewire test confirms existing asset names are not duplicated.
- A feature or Livewire test confirms imported assets are assigned type `Stock`, Bundle `Import`, and Area `Unknown`.
- A feature or Livewire test confirms the displayed transaction count matches the database.
- A feature or Livewire test confirms Refresh updates the transaction count.
- A browser or Livewire test confirms Refresh keeps the Import tab active.
- A feature or Livewire test confirms delete-all requires confirmation.
- A feature or Livewire test confirms cancelling delete-all preserves transactions.
- A feature or Livewire test confirms confirmed delete-all removes transactions without deleting related application data.
- A feature or Livewire test confirms the displayed count becomes `0` after deletion.

## Open Acceptance Items

- Add acceptance criteria for preview, rollback, and error reporting after those behaviors are specified.
