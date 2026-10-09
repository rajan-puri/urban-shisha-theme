# Product catalogue import

## Scope

The verified public ShishaStore export has 107 products and 142 source variants. WooCommerce maps these to 91 simple products, 16 variable products and 51 child variations. All 107 parent products are drafts. Enabled child variation records belong to draft parents and are not live products.

Source prices, including compare-at discounts, are imported into native WooCommerce price fields as reference values. They are not approved Urban Shisha selling prices. Source availability is retained in provenance only: imported products/variations are out of stock, do not manage an invented stock quantity, and do not allow backorders. Confirm native stock/prices before publishing. Zero/missing source weight is left blank rather than treated as a verified weight.

The 304 source image URLs contain 303 distinct file hashes. Media Library deduplicates the identical original and records all source URL aliases. Galleries and variation images retain their source order/mapping. Originals are copied without tinting, cutting out, recompression or automatic large-image replacement; WordPress generates normal thumbnails separately. Opaque source originals stay opaque. The optional showcase image field allows a separately supplied transparent cutout.

## Editable data

WooCommerce owns product title, description, images/gallery, regular/sale price, stock, SKU, barcode, weight, categories, brands and colour attributes/variations. Missing SKUs/barcodes are not invented; the source placeholder `SKU` is not used as a real identifier.

Secure Custom Fields adds the product-only **Urban Shisha — Product details** group:

- Specifications: label/value rows.
- Features: feature, availability and notes.
- Included items: editable rows.
- Usage instructions: step/instruction rows.
- Optional transparent showcase image.
- Content-review status and internal notes.

Recognised specification/item paragraphs move out of the description into their structured fields, so they have one editorial owner. Other prose stays in the WooCommerce description. Missing optional sections stay empty. Original full source content remains in the export for reference. Imported claims, included items and manufacturer names require content review before publication; the importer does not silently rewrite them as Urban Shisha claims.

Source `Shipping & Delivery`, payment promises and FAQ sections are excluded. Explicit source-store free-gift sections are also excluded. Urban Shisha shipping/payment settings remain separate.

## Categories and brands

Structural collections map to Hookahs, Accessories, Portable Hookahs, Bowls, Heat Management, Coal Burners, Hookah Bags, Tongs and Charcoal. Source campaign collections such as offers/best-sellers/budget/fresh-stock are preserved as provenance instead of assigning unapproved Urban Shisha campaigns.

Source vendors COCOYAYA, VG-France and Alshan map to native WooCommerce brand terms. Source-store vendor entries are retained in provenance but are not assumed to be manufacturer brands. Products without a confirmed manufacturer can be assigned their correct brand later in Admin. All source collection handles/tags remain available in post metadata/export.

## Implementation and version control

Durable fields, admin provenance and the CLI importer live in the active sibling `wp-content/plugins/urban-shisha-core` plugin, version 0.2.0. No feature code was added to theme functions.php, CSS, JS or templates.

`companion-plugin/urban-shisha-core/` is the versioned copy of the deployed companion plugin. It includes the existing global/home/page schemas, not just product changes. Keep it in sync when editing the deployed sibling plugin. WordPress loads only the actual sibling plugin, not this theme-folder copy. A theme ZIP alone does not activate/install the companion plugin on another site.

`scripts/prepare-catalog.py` prepares a deterministic JSON plan using BeautifulSoup. `scripts/verify-catalog.php` audits the real WooCommerce database and original image files. Neither is used at frontend runtime. The importer uses WooCommerce CRUD and SCF field keys, following the [WooCommerce CRUD guidance](https://developer.woocommerce.com/docs/best-practices/data-management/crud-objects/) and [field-key update guidance](https://www.advancedcustomfields.com/resources/update_field/).

## Repeatable commands

Use the local WP-CLI/PHP environment or the Local site shell. Do not run parallel imports against the same database.

```sh
python3 -m venv /tmp/urban-catalog-venv
/tmp/urban-catalog-venv/bin/pip install -r /Users/rajan/Downloads/urban-shisha-catalog-export/requirements.txt
/tmp/urban-catalog-venv/bin/python scripts/prepare-catalog.py \
  --data /Users/rajan/Downloads/urban-shisha-catalog-export/data \
  --output /Users/rajan/Downloads/urban-shisha-catalog-export/data/woocommerce-plan.json

wp urban-shisha catalog-import \
  --file=/Users/rajan/Downloads/urban-shisha-catalog-export/data/woocommerce-plan.json \
  --report=/Users/rajan/Downloads/urban-shisha-catalog-export/data/woocommerce-full-import.json

wp eval-file scripts/verify-catalog.php \
  /Users/rajan/Downloads/urban-shisha-catalog-export/data/woocommerce-plan.json \
  /Users/rajan/Downloads/urban-shisha-catalog-export/data/woocommerce-verification.json
```

Use `--only=source-handle,source-handle` for a sample or `--limit=N` for a capped run. Successful products are skipped on reruns, including their edited names, prices, SCF content, stock and publication state. The command is an initial import/resume tool, not a source-store sync; there is no forced-refresh option. Incomplete draft records resume using stable source IDs and existing media; unexpected duplicate IDs or trashed records cause an explicit error. Never reset the completion marker on a product that editors have changed.

The audit is intended for the initial import baseline; intentional later editor changes will understandably differ from the source plan. Per-product import results and independent verification reports are stored outside the public web root in the export data folder.

## Remaining conversion work

The approved frontend product/shop design is not connected by this task. Next, templates must read WooCommerce data and these fields, then price/stock/content must be reviewed before publishing. This import does not configure shipping, payments, homepage product selections or live purchasing flows.
