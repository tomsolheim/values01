# Feature 005: Holdings Table

## Goal

Define the holdings table used to track portfolio positions, purchase values, current prices, exchange rates, and profit values.

## User Story

As a user, I want to store portfolio holdings so I can see purchase value, current value, and profit for each position.

## Data Model

### Holdings Table

Table name: `holdings`

Fields:

- `id`
- `ticker`
- `gav`
- `count`
- `cost`
- `pvalue`
- `price`
- `erate`
- `value`
- `profit`
- `profit_percent`
- `created_at`
- `updated_at`

## Field Definitions

| Label | Field | Purpose |
| --- | --- | --- |
| Tic | `ticker` | Stock ticker or internal identifier. Internal identifiers start with `-int-`. |
| GAV | `gav` | Purchase value in NOK. |
| Count | `count` | Number held in portfolio. |
| Cost | `cost` | Transaction cost. |
| PValue | `pvalue` | Purchase value, not including cost. |
| Price | `price` | Current stock price in native currency. |
| Rate | `erate` | Current exchange rate. |
| Value | `value` | Current value in NOK. |
| Profit | `profit` | Profit in NOK. |
| Profit % | `profit_percent` | Profit percentage in NOK terms. |

## Naming Note

The supplied field list used `profit` for both `Profit` and `Profit %`. The table should use `profit` for the NOK amount and `profit_percent` for the percentage so each column has a distinct field name.

## Validation Rules

- `ticker` is required.
- `ticker` may be a stock ticker or an internal identifier beginning with `-int-`.
- Numeric holding fields should allow decimal values.
- `count` should allow decimal values because some assets may support fractional holdings.
- Empty numeric fields may be stored as `null` until calculation rules are specified.

## Calculation Rules

Calculation behavior is not yet fully specified.

Initial intended meanings:

- `gav` represents purchase value in NOK.
- `pvalue` represents purchase value before transaction cost.
- `value` represents current value in NOK.
- `profit` represents current profit in NOK.
- `profit_percent` represents current profit percentage.

Precise formulas are TBS before implementation.

## UI Placement

The holdings form/list widget belongs in the workbench tab named `tab05`.

In the first implementation step:

- `tab05` no longer only shows `info05`.
- `tab05` shows the holdings widget.
- Calculations are deferred until later specification.

## Widget Behavior

### Form

The widget includes a form for creating or editing a holding.

Form fields and labels:

| Label | Field | Control | Purpose |
| --- | --- | --- | --- |
| Tic | `ticker` | Text input | Stock ticker or internal identifier. |
| GAV | `gav` | Number input | Purchase value in NOK. |
| Count | `count` | Number input | Number held in portfolio. |
| Cost | `cost` | Number input | Transaction cost. |
| PValue | `pvalue` | Number input | Purchase value, not including cost. |
| Price | `price` | Number input | Current stock price in native currency. |
| Rate | `erate` | Number input | Current exchange rate. |
| Value | `value` | Number input | Current value in NOK. |
| Profit | `profit` | Number input | Profit in NOK. |
| Profit % | `profit_percent` | Number input | Profit percentage in NOK terms. |

Expected controls:

- Save button
- Cancel or reset button when editing

### List

The widget includes a list or table of existing holdings.

Visible columns:

- `ticker`
- `gav`
- `count`
- `cost`
- `pvalue`
- `price`
- `erate`
- `value`
- `profit`
- `profit_percent`

Expected row actions:

- Edit
- Delete

## Scope

Included:

- Holdings table specification
- Field labels and purposes
- Basic validation expectations
- Naming resolution for `profit_percent`
- Holdings form/list widget in `tab05`

Not included:

- Calculation formulas
- Relationship to the Asset table
- Currency table
- Price feed integration
- Historical transactions
- Import/export

## Open Questions

- Should `ticker` connect to the Asset table or remain a free/manual identifier?
- Should `gav`, `value`, `profit`, and `profit_percent` be manually entered, calculated, or both?
- Should holdings represent current positions only, or should transactions be stored separately?
