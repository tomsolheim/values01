# Decision 0002: Reuse Frontdemo02 Card Selector and Instance Info

## Status

Accepted

## Context

Values01 needs a card visibility selector in the sidebar and an instance identity card in the top area. Similar widgets already exist in `/tomaco3/htdocs/frontdemo02`.

## Decision

Reuse and adapt the following widgets from `frontdemo02`:

- Card Selector pattern from `customer-card-selector`
- Instance Info from `instance-info`

The Card Selector replaces `side01`.

The Instance Info widget replaces `top09`.

## Consequences

- Existing behavior and visual structure can be reused instead of redesigned.
- The implementation should adapt naming and styling to Values01.
- Unrelated `frontdemo02` dependencies, customer-specific behavior, and customer-specific labels should not be copied unless explicitly specified.
- Controlled card ids for Values01 remain TBS until the final card registry is specified.
