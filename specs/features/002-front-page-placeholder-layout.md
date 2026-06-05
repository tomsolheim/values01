# Feature 002: Front Page Placeholder Layout

## Goal

Define the initial front page layout using a 12-column Bootstrap grid with placeholder cards and tabbed workspace content.

## User Story

As a project owner, I want the front page divided into clear placeholder regions so the application can later grow into a structured working interface.

## Layout

The front page uses a 12-column grid.

### Header

- The page has a compact header area with minimal whitespace before the top cards.
- The `<main>` container uses `py-3` (not `py-5`) to keep top padding compact.
- The top cards row uses `mb-3` (not `mb-4`) to reduce gap before the main area.

### Top Area

- The top area spans all 12 grid columns.
- The top area initially contains three single-box placeholder cards.
- The cards are named:
  - `top01`
  - `top08`
  - `top09`
- `top01` and `top08` are placeholders only in the first step.
- `top09` is replaced by the Instance Info widget reused from `/tomaco3/htdocs/frontdemo02`.
- `top01` is always aligned to the left side of the top area.
- `top08` and `top09` are always aligned to the right side of the top area.
- `top08` and `top09` should appear as a right-side group.
- The spacing between `top08` and `top09` should be compact and consistent with the grid gap.
- The layout may stack on small screens, but desktop layout preserves left/right alignment.

### Instance Info

- The Instance Info widget appears in the `top09` position.
- The widget is based on `frontdemo02`'s `instance-info` widget.
- The widget shows app/runtime identity information.
- Initial fields:
  - Project
  - Hostname
  - IP Address
- The widget should adapt visually to the Values01 Bootstrap 5 layout.

### Main Area

Below the top area, the page is split into:

- Sidebar: 3 grid columns wide
- Workbench: 9 grid columns wide

### Sidebar

- The sidebar contains two placeholder cards.
- The cards are named:
  - `side01`
  - `side02`
- `side01` is replaced by the Card Selector widget reused from `/tomaco3/htdocs/frontdemo02`.
- `side02` remains a placeholder in the first step.

### Card Selector

- The Card Selector widget appears in the `side01` position.
- The widget is based on `frontdemo02`'s `customer-card-selector` / card selector pattern.
- The widget controls the visibility of registered cards or widget panels.
- `top01` is not controlled by the Card Selector.
- `top01` should remain visible regardless of Card Selector state.
- The widget includes:
  - A heading identifying the selector.
  - An `All on` action.
  - An `All off` action.
  - A dropdown or compact list of checkboxes for individual cards.
- Each controlled card or widget panel uses a stable card id.
- Only cards explicitly registered with the Card Selector are affected by it.
- Card visibility controls should stay in sync with the visible cards.
- The widget should adapt visually to the Values01 Bootstrap 5 layout.
- The exact list of controlled cards is TBS before final implementation.

### Workbench

- The workbench is a tabbed area.
- It contains 10 tabs.
- The stable internal tab ids are `tab01` through `tab10`.
- Each tab has a visible human-readable label.
- The active tab must persist during interactions inside that tab.
- Tab persistence applies to pagination, search, edit, delete, save, cancel, and form show/hide actions.
- Interacting with a widget inside one tab must not reset the workbench back to `tab01`.
- If the page reloads or a server round-trip occurs, the application should restore the previously active tab when possible.

Tab registry:

| Tab ID | Visible Label | Initial Content |
| --- | --- | --- |
| `tab01` | `tab01` | `info01` placeholder |
| `tab02` | `Assets` | Asset form/list widget |
| `tab03` | `Bundles` | Bundle form/list widget |
| `tab04` | `Areas` | Area form/list widget |
| `tab05` | `Holdings` | Holdings form/list widget |
| `tab06` | `History` | History list widget |
| `tab07` | `tab07` | `info07` placeholder |
| `tab08` | `tab08` | `info08` placeholder |
| `tab09` | `tab09` | `info09` placeholder |
| `tab10` | `tab10` | `info10` placeholder |

## Scope

Included:

- Front page layout specification
- Header line
- Full-width top area
- Top-area left/right alignment
- 3-column sidebar
- 9-column workbench
- Placeholder cards
- Card Selector in the `side01` position
- Instance Info in the `top09` position
- 10-tab workbench with stable internal ids and visible labels
- Persisted active tab state during widget interactions

Not included:

- Real data
- Business logic
- User-specific state
- Card actions

## UI Notes

- Use Bootstrap 5 grid classes for the 12-column structure.
- Use Bootstrap cards for placeholder boxes.
- Reuse the behavior and visual structure of the `frontdemo02` Card Selector and Instance Info widgets where practical.
- Adapt reused widgets to Values01 naming, layout, and Bootstrap 5 conventions.
- Use Bootstrap 5 grid or flex utilities to keep `top01` left and `top08`/`top09` right on desktop.
- Use Bootstrap tabs for the workbench.
- Use the visible tab labels from the tab registry in the UI.
- Preserve the active Bootstrap tab state during widget interactions.
- Avoid large vertical padding between the header and the top area.
- Prefer compact Bootstrap spacing such as `pt-2` or `pt-3` for the main content top padding.
- Keep the layout functional and quiet, suitable for a working application.

## Edge Cases

- On small screens, the sidebar and workbench may stack vertically.
- Tab labels should remain readable and not overflow awkwardly.
- Placeholder cards should make their region names visible.
- Reused widgets should not introduce unrelated dependencies from `frontdemo02`.
- Reused widgets should not break the 12-column top area or 3-column sidebar layout.
