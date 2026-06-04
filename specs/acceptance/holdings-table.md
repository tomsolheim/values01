# Acceptance: Holdings Table

## Criteria

- The application has a `holdings` table.
- The `holdings` table includes `ticker`, `gav`, `count`, `cost`, `pvalue`, `price`, `erate`, `value`, `profit`, and `profit_percent`.
- The `holdings` table includes timestamps.
- `ticker` is required.
- `ticker` accepts stock tickers.
- `ticker` accepts internal identifiers beginning with `-int-`.
- Numeric fields allow decimal values.
- `profit` stores profit in NOK.
- `profit_percent` stores profit percentage.
- `tab05` in the workbench shows the holdings form/list widget.
- The holdings form uses the labels `Tic`, `GAV`, `Count`, `Cost`, `PValue`, `Price`, `Rate`, `Value`, `Profit`, and `Profit %`.
- The holdings widget can create a holding.
- The holdings widget can list existing holdings.
- The holdings widget can edit an existing holding.
- The holdings widget can delete an existing holding.
- The holdings list shows the columns `ticker`, `gav`, `count`, `cost`, `pvalue`, `price`, `erate`, `value`, `profit`, and `profit_percent`.

## Suggested Tests

- A migration test or database assertion confirms the `holdings` table exists with the expected columns.
- A validation test confirms `ticker` is required.
- A validation test confirms internal identifiers beginning with `-int-` are accepted.
- A validation test confirms decimal values are accepted for numeric fields.
- A feature or Livewire test confirms holdings are listed in `tab05`.

## Open Acceptance Items

- Add acceptance criteria for formulas once calculation rules are specified.
- Add acceptance criteria for Asset table relationships if `ticker` becomes linked to assets.
