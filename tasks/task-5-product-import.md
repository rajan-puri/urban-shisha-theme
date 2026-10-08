# Goal
Create editable product fields and import the verified ShishaStore catalogue into local WooCommerce.

# Context
Owner explicitly authorises direct Codex implementation; agy is used only on explicit request. Existing dirty theme changes belong to earlier work and must be preserved. The approved frontend design is outside this task. All parent products remain draft, stock remains out of stock until confirmed, source prices are reference values. FAQ and source shipping/payment promises must not enter product content.

# Files to touch
- companion-plugin/urban-shisha-core/**: versioned companion plugin source matching the deployed sibling plugin.
- scripts/prepare-catalog.py
- scripts/verify-catalog.php
- PRODUCT-CATALOG-IMPORT.md
- tasks/task-5-product-import.md
- tasks/task-5-plugin-baseline.json
- ../plugins/urban-shisha-core/README.md
- ../plugins/urban-shisha-core/urban-shisha-core.php
- ../plugins/urban-shisha-core/inc/product-admin.php
- ../plugins/urban-shisha-core/inc/catalog-import.php
- ../plugins/urban-shisha-core/fields/group-us-products.json
- Local WordPress database: imported draft products, variations, categories, brand terms, attribute terms, SCF values and image attachments.
- Verified export directory: derived WooCommerce plan and import/verification reports.

# Constraints
Keep functions.php, CSS, JS and existing approved designs unchanged. Use native WC CRUD, WP Media Library and SCF. Stable source identifiers prevent duplicates; skip completed products on reruns so editor changes survive. Preserve original images. Native prices/stock must not be duplicated as editable custom commerce fields. No publish, push, merge, agy or unrelated cleanup.

# Acceptance criteria
- [x] Product-only SCF schemas expose specifications, features, included items, usage and optional showcase image.
- [x] Imported 107 draft parents: simple or variable according to source options.
- [x] All 142 source variants represented across simple products and variation records.
- [x] All 304 originals imported and attached to correct galleries; variation images mapped.
- [x] Source collections/vendor/availability remain traceable; no invented brands.
- [x] No FAQ or source Shipping & Delivery section in customer-facing imported content.
- [x] Native source reference prices are mapped correctly, no fabricated stock quantity, all stock statuses outofstock.
- [x] A representative sample passes before full import; rerunning does not create duplicates or overwrite completed editor data.
- [x] PHP syntax, native field/admin editing and independent database/media assertions pass.

# Feedback
PASS: 107 draft parents (91 simple / 16 variable), 51 variation records and 142 represented source variants; 304 gallery image references, 303 distinct original Media Library images. Audited all original checksums, prices, categories/brands, option/image mappings and SCF rows. 504 specification rows, 536 feature rows, 28 included-item rows and 402 usage steps. SCF editor controls rendered and a repeater edit/read/restore passed. Five-product rerun preserved a temporary native title + SCF edit; restored both. Full rerun skipped all 107 with zero creations/errors. All changed PHP passes syntax; npm lint passes. Existing unrelated theme changes preserved. Sibling plugin scope and versioned-copy equality independently checked. Reports live in the export data folder; frontend product template binding remains a separate task.
