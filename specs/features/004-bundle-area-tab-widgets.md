# Feature 004: Bundle and Area Tab Widgets

## Goal

Define the bundle and area source tables and their form/list widgets in the workbench.

## User Story

As a user, I want to maintain bundles and areas separately so assets can be categorized consistently.

## Data Model

### Bundle Table

Table name: `bundles`

Fields:

- `id`
- `name`
- `comment`
- `created_at`
- `updated_at`

### Area Table

Table name: `areas`

Fields:

- `id`
- `name`
- `comment`
- `created_at`
- `updated_at`

## Field Definitions

`name`

- Human-readable name.
- Required.
- Short text.
- Used as the visible dropdown option in the asset form.

`comment`

- Free-form note.
- Optional.
- Long text.

## UI Placement

### Tab03

- `tab03` contains the Bundle form/list widget.
- `tab03` no longer only shows `info03`.

### Tab04

- `tab04` contains the Area form/list widget.
- `tab04` no longer only shows `info04`.

## Bundle Widget

The Bundle widget includes a form and list.

Form fields:

| Label | Field | Control | Values / Source |
| --- | --- | --- | --- |
| Name | `name` | Text input | Free manual content |
| Comment | `comment` | Textarea | Free manual content |

List columns:

- `name`
- `comment`

Expected row actions:

- Edit
- Delete

## Area Widget

The Area widget includes a form and list.

Form fields:

| Label | Field | Control | Values / Source |
| --- | --- | --- | --- |
| Name | `name` | Text input | Free manual content |
| Comment | `comment` | Textarea | Free manual content |

List columns:

- `name`
- `comment`

Expected row actions:

- Edit
- Delete

## Relationship to Assets

- The Asset form in `tab02` uses `bundles.name` as the visible label for the `bundle_id` dropdown.
- The Asset form in `tab02` uses `areas.name` as the visible label for the `area_id` dropdown.
- An asset may have no bundle.
- An asset may have no area.

## Scope

Included:

- Bundle table specification
- Area table specification
- Bundle form/list widget in `tab03`
- Area form/list widget in `tab04`
- Dropdown source relationship for the Asset widget in `tab02`

Not included:

- Sorting rules
- Filtering
- Import/export
- Authorization rules
- Preventing deletion of a bundle or area used by assets

## UI Notes

- Use Bootstrap 5 form controls and table styling.
- Keep each widget compact and scannable.
- Use the exact labels `Name` and `Comment`.

## Edge Cases

- Empty bundle and area lists should not prevent creating an asset.
- If no bundles exist, the Asset Bundle dropdown should allow no selection.
- If no areas exist, the Asset Area dropdown should allow no selection.
- Deleting a bundle or area that is used by assets needs a later explicit rule before implementation.
