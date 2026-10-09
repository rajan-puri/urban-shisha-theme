/**
 * Urban Shisha — WordPress Live My Account Integration
 *
 * Connects auth tabs (Login / Register), password show/hide toggles,
 * and account wishlist actions with WooCommerce.
 */
(() => {
	'use strict';

	const $ = (s, r = document) => r.querySelector(s);
	const $$ = (s, r = document) => [...r.querySelectorAll(s)];
	const config = window.UrbanAccountConfig || window.UrbanCommerceDrawers || {};
	const ajaxUrl = config.ajaxUrl || '/wp-admin/admin-ajax.php';
	const wishlistNonce = config.wishlistNonce || '';
	const cartNonce = config.cartNonce || '';

	// Auth Tabs
	const tabLogin = $('#tab-login');
	const tabRegister = $('#tab-register');
	const panelLogin = $('#panel-login');
	const panelRegister = $('#panel-register');

	if (tabLogin && tabRegister && panelLogin && panelRegister) {
		tabLogin.addEventListener('click', () => {
			tabLogin.classList.add('is-active');
			tabLogin.setAttribute('aria-selected', 'true');
			tabRegister.classList.remove('is-active');
			tabRegister.setAttribute('aria-selected', 'false');

			panelLogin.hidden = false;
			panelRegister.hidden = true;
		});

		tabRegister.addEventListener('click', () => {
			tabRegister.classList.add('is-active');
			tabRegister.setAttribute('aria-selected', 'true');
			tabLogin.classList.remove('is-active');
			tabLogin.setAttribute('aria-selected', 'false');

			panelRegister.hidden = false;
			panelLogin.hidden = true;
		});
	}

	// Password toggle buttons
	document.addEventListener('click', e => {
		const toggleBtn = e.target.closest('.password-toggle-btn');
		if (toggleBtn) {
			e.preventDefault();
			const targetId = toggleBtn.getAttribute('data-target');
			const input = document.getElementById(targetId);
			if (input) {
				const isPass = input.type === 'password';
				input.type = isPass ? 'text' : 'password';
				toggleBtn.setAttribute('aria-pressed', isPass ? 'true' : 'false');
			}
		}
	});

	// Account Wishlist Actions
	document.addEventListener('click', async e => {
		// Remove from wishlist
		const removeBtn = e.target.closest('[data-action="remove-wishlist"]');
		if (removeBtn) {
			e.preventDefault();
			const card = removeBtn.closest('.wishlist-card');
			const productId = parseInt(removeBtn.getAttribute('data-product-id'), 10);
			if (!productId) return;

			if (card) card.style.opacity = '0.4';

			try {
				const formData = new FormData();
				formData.append('action', 'urban_shisha_toggle_wishlist');
				formData.append('nonce', wishlistNonce);
				formData.append('product_id', productId);
				formData.append('intent', 'remove');

				const res = await fetch(ajaxUrl, { credentials: 'same-origin', method: 'POST', body: formData });
				const data = await res.json();

				if (data.success) {
					window.location.reload();
					return;
				}
				if (card) card.style.opacity = '1';
				window.UrbanCommerce?.showToast(data.data?.message || 'Could not remove this item.');
			} catch (err) {
				if (card) card.style.opacity = '1';
			}
			return;
		}

		// Add to bag from wishlist
		const addBtn = e.target.closest('[data-action="add-to-bag"]');
		if (addBtn) {
			e.preventDefault();
			const productId = parseInt(addBtn.getAttribute('data-product-id'), 10);
			if (!productId) return;

			addBtn.disabled = true;
			try {
				const formData = new FormData();
				formData.append('action', 'urban_shisha_add_to_cart');
				formData.append('nonce', cartNonce);
				formData.append('product_id', productId);
				formData.append('quantity', 1);

				const res = await fetch(ajaxUrl, { credentials: 'same-origin', method: 'POST', body: formData });
				const data = await res.json();

				if (data.fragments) {
					addBtn.textContent = 'Added to bag ✓';
					if (window.jQuery) {
						window.jQuery(document.body).trigger('added_to_cart', [data.fragments, data.cart_hash, window.jQuery(addBtn)]);
					}
					setTimeout(() => {
						addBtn.textContent = 'Add to bag';
						addBtn.disabled = false;
					}, 2000);
				} else {
					addBtn.disabled = false;
				}
			} catch (err) {
				addBtn.disabled = false;
			}
		}
	});
})();
