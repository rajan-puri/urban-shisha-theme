# WooCommerce and CMS setup — 8 October 2026

The owner authorised basic WooCommerce setup and editable field preparation. Product catalogue structure is explicitly awaiting further owner instructions.

## Completed

- Store business address: Rohini Sector 24, Delhi 110085, India; no street/unit number invented.
- Currency INR, symbol before price, two decimal places; metric units kg/cm.
- Initial selling/shipping region India. This is the development baseline, editable under WooCommerce Settings; shipping destinations do not constitute configured shipping rates.
- Guest checkout, checkout sign-in/account creation and My Account registration enabled. WooCommerce handles generated usernames and password setup.
- Existing published Shop (7), Cart (8), Checkout (9), My Account (10) page mappings verified.
- Existing Secure Custom Fields 6.9.5 was already active; no duplicate installation.
- Added and activated `wp-content/plugins/urban-shisha-core`. Theme `functions.php` remains a small bootstrap. No CSS or JavaScript was added to the plugin.
- Three version-controlled SCF groups: global settings, homepage sections and structured editorial page content. Generic editorial fields are excluded from WooCommerce system pages.
- Homepage supports 14 approved section types with reordering, visibility, copy, imagery, links and curated WooCommerce product selections. No product catalogue, categories, brands or attributes created.
- Confirmed business name/domain/location, both owner contacts, existing age confirmation copy and Mates Creation credit seeded into SCF options without overwriting existing editor values. Primary WhatsApp/email remain unconfigured.

## Admin locations

- **Urban Shisha**: branding, announcement, business contacts, mega-menu cards, footer/newsletter and age confirmation copy.
- **Urban Shisha → Homepage content**: add/reorder approved sections and edit their fields.
- **Pages → Edit page**: native page body plus structured intro, panels, FAQ and process fields where appropriate.
- **WooCommerce → Settings**: actual store configuration.

SCF schemas are JSON source files in `urban-shisha-core/fields/` and loaded by `inc/cms.php`. Values live in the WordPress database. Templates are not yet bound to these fields; homepage media/content migration and dynamic rendering are the next conversion tasks. Adding fields does not connect preview checkout/auth/forms to WooCommerce.

## Verification

Native SCF registration/type/unique-key validation and persisted business contact repeater checks passed. Authenticated browser checks verified global values, all 14 homepage layout choices, hero field editing and WooCommerce settings with no JavaScript page errors. Blank test hero content was not saved. PHP syntax checks passed. Reports: `WOOCOMMERCE-CMS-SETUP.json`, `WOOCOMMERCE-CMS-BROWSER.json`.

## Pending decisions and work

Owner instructions for products/categories/brands/attributes; catalogue import; homepage and other approved template conversion; shipping methods/rates; tax/GST decisions; payment configuration; final policies; primary support channels; wishlist and actual enquiry/contact/newsletter handlers.

Shipping rates, gateways and tax settings were not fabricated or configured as part of this task. The approved theme remains inactive; the existing active theme is unchanged.
