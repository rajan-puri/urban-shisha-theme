# Goal
Convert About, Contact, Bulk Orders and five policy pages to approved HTML designs with admin-editable content and working enquiry forms; complete remaining page routing.

# Context
Run after9/10/11. User wants ALL conversion done via agy, same original HTML design, no commits. Source about.html/contact.html/wholesale.html/shipping.html/returns.html/privacy.html/terms.html/age-policy.html, assets/pages.css/contact.css/wholesale.css and respective preview JS. Header/footer dynamic complete. User business is online only, Delhi Rohini Sector24 110085, no physical store. Both owner details editable global; don't invent primary WhatsApp/email/hours/MOQ/rates/shipping promises. Wholesale warehouse animation deferred in WHOLESALE-PLAN.md. Real products (draft for editor preview, published only public); no fake forms or retail cart contamination.

# Files to touch
- functions.php (modular require only)
- inc/assets.php
- inc/pages.php (new)
- page.php (designed page dispatch)
- template-parts/pages/about.php (new)
- template-parts/pages/contact.php (new)
- template-parts/pages/wholesale.php (new)
- template-parts/pages/policy.php (new shared layout)
- assets/pages-wordpress.css (new scoped integration only)
- assets/js/pages-wordpress.js (new)
- assets/js/wholesale-wordpress.js (new)
- scripts/seed-pages.php (new idempotent saved approved content/media)
- companion-plugin/urban-shisha-core/fields/group-us-pages.json
- /Users/rajan/Local Sites/urban-shisha/app/public/wp-content/plugins/urban-shisha-core/fields/group-us-pages.json (matching copy)
- companion-plugin/urban-shisha-core/urban-shisha-core.php
- /Users/rajan/Local Sites/urban-shisha/app/public/wp-content/plugins/urban-shisha-core/urban-shisha-core.php
- companion-plugin/urban-shisha-core/inc/enquiries.php (new)
- /Users/rajan/Local Sites/urban-shisha/app/public/wp-content/plugins/urban-shisha-core/inc/enquiries.php (matching copy)
- tasks/task-12.md (Feedback only)

# Constraints
- file view/edit tools only, no shell/commits/push; preserve prior work and static source.
- Copy original full layouts/styles/illustrations and responsive spacing, all hero/panels/FAQ/process/contact details/policy TOC. Main content/copy/media/cards/FAQ/policy bodies are native WP content or SCF saved fields; seed approved source only once. No runtime HTML parsing. Remove obsolete preview-only wording where backend now real; don't claim policies operationally final or add unprovided business promises.
- Contact and wholesale forms use core plugin stored private enquiry records, validated nonce/email/phone/lengths/product IDs/quantities, rate limit/spam protection, truthful accessible success/error; no external email/WhatsApp messages unless configured (do not send emails).
- Wholesale catalogue real WC product names/images, source rates never labelled wholesale; quote requests with quantities1-9999; server validates actual visibility IDs. Separate persisted guest enquiry shortlist from retail cart. Don't add deferred3D animation.
- Privacy copy should accurately describe actual WC account/cart/wishlist/newsletter/enquiry data, not claim preview-only in-memory storage. Keep legal/operational specifics editable draft admin content, no invented policy promises.
- Seed existing About14 Contact15 Bulk16 Shipping17 Returns11 Privacy3 Terms18 Age19 via script; manager executes. Publishing local content pages allowed while ComingSoon stays on. Preserve editor changes and page names/slugs, don't populate unrelated pages.
- Policy pages no unnecessary age gate; About/Contact/Bulk follow shared18+ behavior. GSAP shared reveal guarded/reduced-motion; no global cursor dot.

# Acceptance criteria
- [ ] All remaining pages match approved full desktop/mobile design and are admin editable.
- [ ] Contact/bulk requests persist server-side only after valid explicit submission, no fake success.
- [ ] Real catalogue selection safe, quantities validated, no draft leaks or retail cart contamination.
- [ ] Correct canonical routing/nav/policy anchors/accordion/GSAP; seed idempotent and editor values preserved.
- [ ] No errors/overflow or regressions; both plugin copies/schemas match.

# Feedback
Pending implementation after task11.
