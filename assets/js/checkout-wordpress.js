/**
 * Urban Shisha — WordPress Live Checkout Integration
 *
 * Connects WooCommerce single-page checkout form, billing/shipping toggle,
 * age confirmation validation, duplicate submission protection, and total sync.
 */
(() => {
	'use strict';

	const $ = (s, r = document) => r.querySelector(s);
	const $$ = (s, r = document) => [...r.querySelectorAll(s)];

	// Billing same checkbox toggle
	const billingSameCheckbox = $('#billing-same');
	const altBillingFields = $('#alternate-billing-fields');

	function syncBillingWithShippingIfSame() {
		if (billingSameCheckbox && billingSameCheckbox.checked) {
			['first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country'].forEach(field => {
				const shipEl = $(`#shipping_${field}`);
				const billEl = $(`#billing_${field}`);
				if (shipEl && billEl) {
					billEl.value = shipEl.value;
				}
			});
		}
	}

	if (billingSameCheckbox && altBillingFields) {
		billingSameCheckbox.addEventListener('change', () => {
			const isSame = billingSameCheckbox.checked;
			altBillingFields.hidden = isSame;
			syncBillingWithShippingIfSame();

			if (window.jQuery) {
				window.jQuery(document.body).trigger('update_checkout');
			}
		});
	}

	// Trigger checkout recalculation and billing sync on delivery address changes
	['shipping_first_name', 'shipping_last_name', 'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city'].forEach(fieldId => {
		const el = $(`#${fieldId}`);
		if (el) {
			el.addEventListener('input', syncBillingWithShippingIfSame);
			el.addEventListener('change', () => {
				syncBillingWithShippingIfSame();
				if (window.jQuery) {
					window.jQuery(document.body).trigger('update_checkout');
				}
			});
		}
	});

	// Initial sync on page load if billing-same is checked
	syncBillingWithShippingIfSame();

	// Create account toggle
	const createAccountCheckbox = $('#createaccount');
	const createAccountBox = $('.create-account');
	if (createAccountCheckbox && createAccountBox) {
		createAccountCheckbox.addEventListener('change', () => {
			createAccountBox.hidden = !createAccountCheckbox.checked;
		});
	}

	// Client-side validation before submit
	const form = $('.woocommerce-checkout');
	const submitBtn = $('#place_order');

	if (form && submitBtn) {
		form.addEventListener('submit', e => {
			let hasError = false;

			// Confirm 18+ age check
			const confirmAge = $('#confirm-age');
			const errorAge = $('#error-confirm_age');
			if (confirmAge && !confirmAge.checked) {
				hasError = true;
				if (errorAge) {
					errorAge.textContent = 'You must confirm that you are 18 years of age or older.';
					errorAge.hidden = false;
				}
				confirmAge.focus();
			} else if (errorAge) {
				errorAge.hidden = true;
			}

			// Terms check
			const confirmTerms = $('#confirm-terms');
			const errorTerms = $('#error-confirm_terms');
			if (confirmTerms && !confirmTerms.checked) {
				hasError = true;
				if (errorTerms) {
					errorTerms.textContent = 'You must agree to the terms & conditions and privacy policy.';
					errorTerms.hidden = false;
				}
				if (!confirmAge || confirmAge.checked) {
					confirmTerms.focus();
				}
			} else if (errorTerms) {
				errorTerms.hidden = true;
			}

			if (hasError) {
				e.preventDefault();
				return;
			}

			// Prevent rapid duplicate clicks
			submitBtn.disabled = true;
			setTimeout(() => {
				submitBtn.disabled = false;
			}, 4000);
		});
	}

	/**
	 * Sync visible order summary cards with WooCommerce calculations.
	 * Only render price/text in summaries to prevent duplicate radio controls.
	 */
	function syncVisibleOrderSummary() {
		const table = $('.woocommerce-checkout-review-order-table');
		if (!table) return;

		// Subtotal
		const subtotalTd = table.querySelector('.cart-subtotal td');
		if (subtotalTd) {
			const subtotalHtml = subtotalTd.innerHTML;
			const el1 = $('#summary-subtotal');
			const el2 = $('#mobile-calc-subtotal');
			if (el1) el1.innerHTML = subtotalHtml;
			if (el2) el2.innerHTML = subtotalHtml;
		}

		// Shipping: render only price or clean textual status, never controls
		const shippingTd = table.querySelector('.woocommerce-shipping-totals td, .shipping td');
		if (shippingTd) {
			const amountEl = shippingTd.querySelector('.woocommerce-Price-amount');
			let shippingDisplay = '';
			if (amountEl) {
				shippingDisplay = amountEl.outerHTML;
			} else {
				const clone = shippingTd.cloneNode(true);
				clone.querySelectorAll('input, select, button, script, style, label').forEach(el => el.remove());
				const cleanText = clone.textContent.trim();
				shippingDisplay = /free/i.test(cleanText) ? 'Free' : (cleanText || 'Calculated at checkout');
			}
			$$('.summary-shipping-status').forEach(el => {
				el.innerHTML = shippingDisplay;
			});
		}

		// Taxes
		const taxesTd = table.querySelector('.tax-total td, .tax-rate td');
		if (taxesTd) {
			const taxEl = $('#summary-taxes');
			if (taxEl) taxEl.innerHTML = taxesTd.innerHTML;
		}

		// Total
		const totalTd = table.querySelector('.order-total td');
		if (totalTd) {
			const totalHtml = totalTd.innerHTML;
			const el1 = $('#summary-total');
			const el2 = $('#mobile-calc-payable');
			const el3 = $('#mobile-summary-total');
			if (el1) el1.innerHTML = totalHtml;
			if (el2) el2.innerHTML = totalHtml;
			if (el3) el3.innerHTML = totalHtml;
		}
	}

	// Intercept and correct outgoing AJAX request values for update_order_review
	if (window.jQuery) {
		window.jQuery.ajaxPrefilter((options) => {
			if (options.url && options.url.indexOf('update_order_review') !== -1 && options.data) {
				const isSame = $('#billing-same') ? $('#billing-same').checked : true;
				const sCountry = $('#shipping_country')?.value || 'IN';
				const sState = $('#shipping_state')?.value || '';
				const sPostcode = $('#shipping_postcode')?.value || '';
				const sCity = $('#shipping_city')?.value || '';
				const sAddress = $('#shipping_address_1')?.value || '';
				const sAddress2 = $('#shipping_address_2')?.value || '';

				const bCountry = isSame ? sCountry : ($('#billing_country')?.value || 'IN');
				const bState = isSame ? sState : ($('#billing_state')?.value || '');
				const bPostcode = isSame ? sPostcode : ($('#billing_postcode')?.value || '');
				const bCity = isSame ? sCity : ($('#billing_city')?.value || '');
				const bAddress = isSame ? sAddress : ($('#billing_address_1')?.value || '');
				const bAddress2 = isSame ? sAddress2 : ($('#billing_address_2')?.value || '');

				try {
					const params = new URLSearchParams(options.data);
					params.set('s_country', sCountry);
					params.set('s_state', sState);
					params.set('s_postcode', sPostcode);
					params.set('s_city', sCity);
					params.set('s_address', sAddress);
					params.set('s_address_2', sAddress2);

					params.set('country', bCountry);
					params.set('state', bState);
					params.set('postcode', bPostcode);
					params.set('city', bCity);
					params.set('address', bAddress);
					params.set('address_2', bAddress2);

					if (sPostcode || sState) {
						params.set('has_full_address', '1');
					}

					options.data = params.toString();
				} catch (e) {}
			}
		});

		window.jQuery(document.body).on('updated_checkout', () => {
			syncVisibleOrderSummary();

			// Check if WooCommerce returned notices
			const errorNotice = $('.woocommerce-NoticeGroup-checkout');
			if (errorNotice && submitBtn) {
				submitBtn.disabled = false;
			}
		});

		window.jQuery(document.body).on('checkout_error', () => {
			if (submitBtn) {
				submitBtn.disabled = false;
			}
		});
	}
})();
