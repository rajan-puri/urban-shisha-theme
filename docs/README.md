# Urban Shisha WordPress theme

Run the website through Local WordPress (`http://urban-shisha.local/`). The static HTML preview and its server are retired.

## Theme structure

- `pages/`: designed page templates and the generic editable page template.
- `inc/page-templates.php`: adds organized templates to the native WordPress page hierarchy while retaining root/child-theme overrides.
- `woocommerce/`: product, cart, checkout and account template overrides.
- `inc/`: modular WordPress/WooCommerce integration.
- `template-parts/`: shared components.
- `assets/`: styles, scripts, fonts and theme images.
- `docs/`: design and project documentation.

WordPress entry files (`functions.php`, `style.css`, `index.php`, `front-page.php`, `header.php`, `footer.php`, `single.php`, `404.php`, `woocommerce.php`) remain in the theme root. `AGENTS.md` stays at the root for coding tools.

## Checks and CSS build

Run `npm run lint` and `npm run build` from the theme root. Build combines CSS sources into `assets/site.css` and validates the WordPress structure; it does not create HTML previews or `dist/`. PHP syntax and WordPress browser checks are separate.

Read [design.md](design.md) for the approved visual language. Legacy preview instructions in historical documents are superseded by this structure.
