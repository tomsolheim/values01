# Acceptance: Asset Table and Tab02 Widget

## Criteria

- The application has an `assets` table.
- The `assets` table includes `type`, `isin`, `ticker`, `country`, `name`, `bundle_id`, `area_id`, and `comment`.
- The `assets` table includes timestamps.
- `type` is required.
- `name` is required.
- `isin`, `ticker`, `country`, `bundle_id`, `area_id`, and `comment` are optional.
- `isin` is unique when supplied.
- `bundle_id` references the `bundles` table.
- `area_id` references the `areas` table.
- `tab02` in the workbench shows the asset form/list widget.
- The asset form uses the label `Type` for `type`.
- The asset form uses the label `ISIN` for `isin`.
- The asset form uses the label `Tic` for `ticker`.
- The asset form uses the label `Country` for `country`.
- The asset form uses the label `Name` for `name`.
- The asset form uses the label `Bundle` for `bundle_id`.
- The asset form uses the label `Area` for `area_id`.
- The asset form uses the label `Comment` for `comment`.
- The `type` field is a dropdown with `Stock`, `Bank`, `Fund`, and `Other`.
- The `country` field is a dropdown with `NO`, `SE`, `DK`, `DE`, `F`, `ES`, `US`, `UK`, and `Other`.
- The `bundle_id` field is a dropdown populated from the `bundles` table.
- The `area_id` field is a dropdown populated from the `areas` table.
- The `name` and `comment` fields allow free manual content.
- The widget can create an asset.
- The widget can list existing assets.
- The widget can edit an existing asset.
- The widget can delete an existing asset.
- The asset list shows the columns `type`, `isin`, `ticker`, `country`, `name`, `bundle`, `area`, and `comment`.
- The asset list displays bundle and area names rather than raw ids.

## Suggested Tests

- A migration test or database assertion confirms the `assets` table exists with the expected columns.
- A feature or Livewire test confirms a user can create an asset with required fields.
- A feature or Livewire test confirms optional fields may be empty.
- A feature or Livewire test confirms assets are listed in `tab02`.
- A feature or Livewire test confirms an asset can be updated.
- A feature or Livewire test confirms an asset can be deleted.
