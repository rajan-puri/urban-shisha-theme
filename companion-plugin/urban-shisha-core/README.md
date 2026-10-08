# Urban Shisha Core

Content schemas live in `fields/group-us-*.json`, registered by `inc/cms.php` on SCF initialization. Edit these source files for schema changes; content values are saved through SCF to the WordPress database. PHP-registered field groups appear as Local JSON/code groups in SCF tools; they are deliberately version-controlled, not editable duplicate DB schemas.

Admin: Urban Shisha → global settings. The standalone Homepage content submenu was removed at the owner's request; its schema remains registered for the later homepage conversion. Structured page fields are available on WordPress Pages. Existing main page body and policy content uses the native editor.

The plugin works independently of the active theme. Product-only fields now include specifications, features, included items, usage steps, an optional showcase image and internal content review. Native WooCommerce owns product prices, stock, images, brands, categories and variations. Templates still require conversion/binding; wishlist and enquiry submission handlers are pending. Do not duplicate WooCommerce product prices or stock here.

`inc/product-admin.php` displays read-only import provenance in the product editor. `inc/catalog-import.php` is loaded only by WP-CLI and registers `wp urban-shisha catalog-import`: a draft-only local catalogue import/resume command. Completed records are skipped to preserve editor changes. It never treats source-store availability as Urban Shisha stock. There is no automatic source sync.

The theme's PRODUCT-CATALOG-IMPORT.md documents the verified import and commands. Its companion-plugin/urban-shisha-core directory is a versioned copy of this sibling plugin; keep both copies in sync. Plugin version: 0.2.0.

No public CSS or JS is added by this plugin. No store settings change on plugin activation or request. One-time setup is recorded in the theme's WOOCOMMERCE-CMS-SETUP.md.
