# Task 2 — Dynamic PHP header, footer and age confirmation

## Goal

Bind the approved shared PHP shell to saved SCF settings and native WordPress menus. Provide a one-time seed command so existing approved content is editable rather than hardcoded. Preserve the approved look.

## Context

Work in the current theme root. Read AGENTS.md, design.md, BUSINESS-DETAILS.md and DYNAMIC-CMS-SCOPE.md. Forge is active; read `/Users/rajan/.gemini/antigravity-cli/skills/forge/SKILL.md` and all five rules there. Codex Manager owns Git and verification; you only implement the files below. Do not recursively delegate.

The theme is inactive; do NOT activate it. SCF and WooCommerce are active. The sibling urban-shisha-core plugin already registers global options. Read its `fields/group-us-global.json` for exact names/types. Do not edit the plugin.

Important global names: brand_name, brand_logo, brand_logo_light (image IDs), announcement_enabled/text/link, mega_eyebrow/heading/cards (rows mega_card_label/image/link), business_location, owners (owner_name/phone), support_email, primary_whatsapp, support_hours, instagram_url, social_links (social_label/url), footer_description, newsletter_eyebrow/heading/description/placeholder/note, footer_copyright, credit_label/link, all age_* fields and health_notice. SCF link values are title/url/target arrays. Native registered menu locations: primary, footer-shop, footer-discover, footer-care, footer-account.

## Files to touch

- `functions.php` — add one module include only, keep bootstrap clean.
- `inc/content.php` — new shared setting/branding/link/menu helpers with safe escaping and SCF-unavailable handling.
- `template-parts/site-header.php` — preserve icon library and shared classes/IDs; dynamic header, native menus, shared mega-card data on desktop/mobile.
- `template-parts/site-footer.php` — dynamic copy, WordPress menu columns, saved business contacts/social links/credit; preserve responsive accordion hooks.
- `template-parts/age-gate.php` — saved editable age content, preserving dialog controls and accessibility.
- `style.css` — narrowly scoped WordPress menu and uploaded logo/photo integration only, no redesign.
- `scripts/seed-shell.php` — create idempotent WP-CLI-only migration script, never loaded on public requests.

## Constraints

- Touch only allowlisted files. Do not edit task documents, static HTML, generated assets, vendor scripts, plugin files or database directly. Manager runs seed script after review. Do not run shell commands: use file-read/write/edit tools; Manager does build/testing/Git. Report tool-denial truthfully; never use permission bypass.
- Preserve typography, colours, spacing, classes/IDs and existing icons. Standard translatable interface labels may remain in code; main marketing content must come from saved fields. Do not hide incomplete dynamic conversion behind permanent hardcoded sample arrays.
- Native WordPress menus must control desktop/mobile links and footer links. Use a marked native primary Shop menu item (e.g. `urban-mega-trigger` CSS class) to render the existing mega toggle while preserving the Shop destination inside the panel. Support primary items without that marker and nested menu links accessibly. Footer headings can use the assigned menu names.
- Uploaded images must use Media Library IDs, retain transparency, and render naturally above decorative backgrounds. Do not use art-N placeholders/catalog JS to populate PHP mega cards. Logos must have sensible sizing and brand-name alt text; text branding must render when no logo is selected.
- Announcement enabled=false must hide it; clearing optional social/contact/image content hides that element. Escape all content/URLs/attributes; allow appropriate rich/plain text only, never arbitrary script markup or unsafe protocols. No invented store, email, WhatsApp, hours or shipping promises. Render both confirmed owner contacts as callable phone links and business location without storefront claims.
- Account disclosure remains accessible and state-aware: guest sign-in/register; logged-in account details and nonce-protected WooCommerce logout link. Existing order/address endpoints and wishlist placeholder remain; do not claim wishlist persistence exists.
- Server-render real cart quantity from WooCommerce when available, 0 otherwise. Keep `.cart-count` for Task 3 fragment updates. Use unique IDs.
- Seed script: explicit WP-CLI guard, require needed APIs, report actions; create/assign missing primary/footer menus for this theme without replacing existing assigned menus; seed only absent option values (not false/blank editor values). Import the existing local transparent mega-card images into Media Library with an idempotent source marker. Seed existing approved announcement/mega/footer/newsletter copy and credit, and current primary/footer navigation destinations. Do not create products/taxonomies or replace page contents. Do not activate the theme or change active-theme menu assignments; store this theme's nav_menu_locations using theme-mod APIs for the inactive theme. No public/request-time DB writes. User has authorised this specific content/menu/media migration.
- Current categories await owner instructions; retain approved editable category card destinations as Shop query links, don't fabricate category terms. About/contact/policy pages will be converted later.

## Acceptance criteria

- [ ] Setting changes in SCF options are reflected in rendered header/footer/age dialog; optional blanks do not show fake content.
- [ ] Assigned native menus drive desktop/mobile/footer links, including edited labels/URLs; structural classes preserve approved layout.
- [ ] Mega cards use saved labels, link arrays and attachment images on desktop/mobile; no hardcoded catalogue imagery in PHP shell.
- [ ] Both contacts/location and configured social links are rendered safely; no unconfirmed WhatsApp destination.
- [ ] Guest/logged-in account states and actual cart quantity render correctly.
- [ ] Seed command is repeatable, preserves editor values/menu assignments and creates no duplicate media/menu items on rerun.
- [ ] Theme stays inactive; products untouched; PHP syntax and independent WordPress rendering checks pass.
- [ ] Only allowlisted files changed, no feature code in functions.php beyond the include.

## Feedback

### Attempt 1 — Read permission blocked

- Worker exit code 0, but no implementation. Exact relevant output: `jetski: no output produced — a tool required the "read_file" permission that headless mode cannot prompt for, so it was auto-denied.`
- Temporary write settings were restored byte-for-byte. Manager will add exact-file read permissions for the task, project context and installed Forge rules for attempt 2. No shell permissions or permission bypass.

### Attempt 2 — Timeout and incomplete acceptance

- Exact worker output: `[agy] print timeout after 10m0s with turn in progress; returning partial output`. Exit 0 does not mean completion. Original CLI settings restored.
- Manager inspected all changed files; only allowlisted paths changed. PHP syntax checks for 5 changed/new PHP files passed; git diff --check passed.
- FAIL: `scripts/seed-shell.php` does not exist (`ls: scripts/seed-shell.php: No such file or directory`). Implement it first in the final attempt. Keep it concise and idempotent; no shell commands are needed.
- FAIL: `urban_shisha_get_option` treats cleared empty strings as missing and returns hardcoded defaults. Independent check using `acf/load_value/name=footer_description` set to empty string returned `HARDCODED FALLBACK`. Preserve deliberately saved empty/false values. Templates must NOT supply hardcoded marketing defaults. Seed approved marketing copy into DB instead. Required age-button/title safety labels can use translatable functional defaults, but optional copy must hide when cleared.
- FAIL: native menu helpers and footer template contain permanent default navigation arrays. Remove fallback navigation; unassigned/empty native menus must remain empty (or show an admin-only setup notice). Seed approved menus into DB once. Ensure at most one mega-toggle ID even if multiple menu items have the marker. Mobile must include nested menu links, not silently discard children.
- FAIL: footer-wordmark letters remain hardcoded URBAN SHISHA even when brand_name changes. Render escaped letters from the saved brand name.
- FAIL: unconfigured Instagram and WhatsApp render pretend channel buttons. Hide absent social channels; keep a truthful general contact link. Move inline presentation styles into allowlisted style.css. Style configured social anchors consistently with the original social buttons; retain transparent images and approved spacing.
- Other priorities: preserve blank credit behavior (no fabricated URL), validate link URL after sanitizing, guarantee menu parent/child accessibility. Do not rewrite already-correct icon/account/cart markup unnecessarily.
- Final attempt: continue the partial implementation; do not start over or spend the turn re-auditing unchanged documents. Finish seed script and these precise corrections. No additional feature files beyond the original allowlist.
