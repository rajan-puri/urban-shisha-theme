# Urban Shisha — WordPress handoff

This package contains the current editable front-end source. It is not yet an installable WordPress theme or a connected WooCommerce store. Do not upload this source ZIP through Appearance → Themes → Upload Theme expecting activation.

The ZIP contains one `urban-shisha/` folder. Extract it under your WordPress development installation's `wp-content/themes/` directory if that is where you want to continue the conversion. Keep your active theme in place while the conversion is developed. Use `design.md` as the design reference and preserve the approved layouts and behaviour.

## Included

- All 14 HTML pages: Home, Shop, Product, Cart, Checkout, Account, Contact, Bulk Orders, About and five policies.
- Editable CSS, JavaScript, bundled CSS, local GSAP, fonts, photos and brand assets.
- Build/verification/preview scripts, package metadata and design/content handoff notes.

Generated `dist/`, review screenshots/reports, dependency folders and system files are excluded from the source ZIP. `npm run build` recreates the distribution and CSS bundle; edit CSS sources rather than `assets/site.css`.

## Next conversion work

1. Add the WordPress theme structure, including a root theme stylesheet header, `functions.php`, PHP templates and template parts.
2. Split and reuse the existing header/footer; enqueue scripts/styles with theme URLs and correct dependencies. Convert relative HTML/image links to proper WordPress URLs and permalinks.
3. Replace the preview catalogue/cart/checkout/account flows with WooCommerce products, sessions, templates, authentication and order handling. Do not treat local browser data as authoritative commerce data.
4. Connect the real contact/enquiry channels and approved store details. Check `assets/js/config.js` and `POLICY-REVIEW.md`; the business settings and final policy terms remain unconfirmed.
5. Test the converted theme on desktop/mobile and confirm real commerce behaviour before activation on the live store.

The shared account header menu is in `assets/navigation.css` and `assets/js/navigation.js`. Keep it present on all pages. Bulk Orders uses `wholesale.html`; the proposed warehouse/truck React Three Fiber scene is saved in `WHOLESALE-PLAN.md` and remains deferred.
