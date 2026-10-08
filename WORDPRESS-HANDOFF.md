# Urban Shisha — WordPress handoff

The original source ZIP contained the editable static front end. This working folder now also contains a valid WordPress theme foundation (Urban Shisha 0.1.0). It is recognised by WordPress but remains inactive while the approved homepage and commerce are converted. The earlier source ZIP has not been regenerated with these PHP files.

The ZIP contains one `urban-shisha/` folder. Extract it under your WordPress development installation's `wp-content/themes/` directory if that is where you want to continue the conversion. Keep your active theme in place while the conversion is developed. Use `design.md` as the design reference and preserve the approved layouts and behaviour.

## Included

- All 14 HTML pages: Home, Shop, Product, Cart, Checkout, Account, Contact, Bulk Orders, About and five policies.
- Editable CSS, JavaScript, bundled CSS, local GSAP, fonts, photos and brand assets.
- Build/verification/preview scripts, package metadata and design/content handoff notes.

Generated `dist/`, review screenshots/reports, dependency folders and system files are excluded from the source ZIP. `npm run build` recreates the distribution and CSS bundle; edit CSS sources rather than `assets/site.css`.

## Conversion sequence (foundation completed)

1. **Completed:** root theme metadata, functions, PHP fallbacks, shared header/footer, features/menu registration, asset queues and URL helpers. Setup/database/WooCommerce connection checks pass.
2. **Next:** convert the approved homepage into `front-page.php`, connect its page-specific scripts/assets and preview before theme activation. The shared header/footer and WordPress URLs are already prepared.
3. Replace the preview catalogue/cart/checkout/account flows with WooCommerce products, sessions, templates, authentication and order handling. Do not treat local browser data as authoritative commerce data.
4. Connect the real contact/enquiry channels and approved store details. Check `assets/js/config.js` and `POLICY-REVIEW.md`; the business settings and final policy terms remain unconfirmed.
5. Test the converted theme on desktop/mobile and confirm real commerce behaviour before activation on the live store.

The shared account header menu is in `assets/navigation.css` and `assets/js/navigation.js`. Keep it present on all pages. Bulk Orders uses `wholesale.html`; the proposed warehouse/truck React Three Fiber scene is saved in `WHOLESALE-PLAN.md` and remains deferred.
