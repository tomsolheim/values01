# Acceptance: Front Page Placeholder Layout

## Criteria

- The front page shows a header line.
- The spacing between the header line and the top area is compact.
- The header-to-top-area spacing does not look like a large page-section gap.
- The top area spans the full 12-column width.
- The top area shows `top01`, `top08`, and Instance Info in the `top09` position.
- `top01` is aligned to the left side of the top area on desktop.
- `top08` and Instance Info in the `top09` position are aligned to the right side of the top area on desktop.
- `top08` and Instance Info appear as a right-side group on desktop.
- Instance Info shows Project, Hostname, and IP Address.
- The main content area has a sidebar that is 3 grid columns wide on desktop.
- The main content area has a workbench that is 9 grid columns wide on desktop.
- The sidebar shows Card Selector in the `side01` position.
- The sidebar shows placeholder card `side02`.
- Card Selector includes `All on` and `All off` actions.
- Card Selector includes individual checkbox controls for registered cards or widget panels.
- Card Selector visibility controls stay in sync with visible cards.
- Card Selector does not control `top01`.
- `top01` remains visible after using Card Selector `All off`.
- The workbench has 10 stable internal tab ids named `tab01` through `tab10`.
- The workbench shows visible tab labels `tab01`, `Assets`, `Bundles`, `Areas`, `Holdings`, `History`, `tab07`, `tab08`, `tab09`, and `tab10`.
- Each tab shows matching placeholder content from `info01` through `info10`.
- The active tab persists during interactions inside that tab.
- Pagination inside a tab keeps the user on the same tab.
- Search inside a tab keeps the user on the same tab.
- Form show/hide inside a tab keeps the user on the same tab.
- Edit, delete, save, and cancel actions inside a tab keep the user on the same tab.
- Widget interactions do not reset the workbench back to `tab01`.
- The layout remains usable on small screens.

## Suggested Tests

- A feature test confirms the front page contains `top01`, `top08`, and Instance Info.
- A browser or visual test confirms `top01` is left aligned and `top08`/Instance Info are right aligned on desktop.
- A feature test confirms Instance Info contains Project, Hostname, and IP Address labels.
- A feature test confirms the front page contains Card Selector and `side02`.
- A browser test confirms Card Selector can hide and show a registered card.
- A browser test confirms Card Selector `All off` does not hide `top01`.
- A feature test confirms the front page contains the visible tab labels from the tab registry.
- A browser or visual test confirms the header-to-top-area spacing is compact.
- A browser or Livewire test confirms each tab can reveal its matching `info` placeholder.
- A browser or Livewire test confirms Bundle pagination keeps the user on the `Bundles` tab.
- A browser or Livewire test confirms widget interactions do not reset the workbench to `tab01`.
