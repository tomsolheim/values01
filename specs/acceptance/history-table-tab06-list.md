# Acceptance: History Table and Tab06 List

## Criteria

- The application has a `history` table.
- The `history` table includes `isin`, `tic`, `date`, `type`, `count`, `price`, `rate`, `value`, `cost`, `acc`, and `total`.
- The `history` table includes timestamps.
- Numeric fields allow decimal values.
- `tab06` in the workbench shows the history list widget.
- The history list shows the columns `isin`, `tic`, `date`, `type`, `count`, `price`, `rate`, `value`, `cost`, `acc`, and `total`.
- The history list has a search field.
- The history list is paginated.
- The history widget includes a selector with `Ticker` and `ISIN` options.
- Selecting `Ticker` filters the list by `tic`.
- Selecting `ISIN` filters the list by `isin`.

## Suggested Tests

- A migration test or database assertion confirms the `history` table exists with the expected columns.
- A feature or Livewire test confirms the history list is available in `tab06`.
- A feature or Livewire test confirms selecting `Ticker` filters by `tic`.
- A feature or Livewire test confirms selecting `ISIN` filters by `isin`.
- A validation test confirms decimal values are accepted for numeric fields.

## Open Acceptance Items

- Add acceptance criteria for whether an empty filter shows all rows or no rows.
- Add acceptance criteria for whether history rows are manually entered or generated.
