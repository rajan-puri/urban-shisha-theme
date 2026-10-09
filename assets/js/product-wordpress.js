/* Real product gallery and WooCommerce purchases; no demo cart/catalog. */
(() => {
  'use strict';
  const config = window.UrbanProductConfig;
  const root = document.querySelector('.urban-pdp');
  if (!root || !config) return;
  document.body.classList.add('product-page');
  let gallery = [...config.gallery], index = 0;
  const originalGallery = [...gallery];
  const dialog = document.getElementById('gallery-dialog');
  const image = (entry) => { const img = document.createElement('img'); img.src = entry.src; img.alt = entry.name || ''; return img; };
  function render() {
    const entry = gallery[index];
    document.getElementById('gallery-art').replaceChildren(image(entry));
    root.querySelector('.pdp-stage').dataset.view = entry.type;
    const counter = `${String(index + 1).padStart(2, '0')} / ${String(gallery.length).padStart(2, '0')}`;
    document.getElementById('gallery-counter').textContent = counter;
    document.getElementById('zoom-counter').textContent = counter;
    document.getElementById('gallery-enlarged').replaceChildren(image(entry));
    document.getElementById('gallery-status').textContent = `Image ${index + 1} of ${gallery.length}`;
    root.querySelectorAll('[data-gallery-index]').forEach((button) => button.setAttribute('aria-pressed', String(Number(button.dataset.galleryIndex) === index)));
    ['gallery-prev', 'gallery-next', 'zoom-prev', 'zoom-next'].forEach((id) => { document.getElementById(id).disabled = gallery.length < 2; });
  }
  function move(delta) { index = (index + delta + gallery.length) % gallery.length; render(); }
  ['gallery-prev', 'zoom-prev'].forEach((id) => document.getElementById(id).addEventListener('click', () => move(-1)));
  ['gallery-next', 'zoom-next'].forEach((id) => document.getElementById(id).addEventListener('click', () => move(1)));
  root.querySelectorAll('[data-gallery-index]').forEach((button) => button.addEventListener('click', () => { gallery = [...originalGallery]; index = Number(button.dataset.galleryIndex); render(); }));
  document.getElementById('gallery-zoom').addEventListener('click', () => { render(); dialog.showModal(); });
  document.getElementById('gallery-close').addEventListener('click', () => dialog.close());
  dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
  dialog.addEventListener('keydown', (event) => { if (event.key === 'ArrowLeft') move(-1); if (event.key === 'ArrowRight') move(1); });
  let touchX = null;
  const stage = root.querySelector('.pdp-stage');
  stage.addEventListener('touchstart', (event) => { touchX = event.touches[0].clientX; }, { passive: true });
  stage.addEventListener('touchend', (event) => { if (touchX !== null && Math.abs(event.changedTouches[0].clientX - touchX) > 60) move(event.changedTouches[0].clientX < touchX ? 1 : -1); touchX = null; }, { passive: true });
  root.addEventListener('click', (event) => {
    const button = event.target.closest('[data-quantity]');
    if (!button) return;
    const input = button.closest('.pdp-quantity').querySelector('input.qty');
    const step = Number(input.step) || 1;
    const minimum = Number(input.min) || 1;
    const maximum = Number(input.max) > 0 ? Number(input.max) : Infinity;
    input.value = String(Math.max(minimum, Math.min(maximum, (Number(input.value) || minimum) + Number(button.dataset.quantity) * step)));
    input.dispatchEvent(new Event('change', { bubbles: true }));
  });
  const status = document.getElementById('pdp-purchase-status');
  const forms = root.querySelectorAll('form.cart');
  forms.forEach((form) => form.addEventListener('submit', async (event) => {
    if (!form.matches('.pdp-cart, .variations_form')) return;
    event.preventDefault();
    const button = form.querySelector('.single_add_to_cart_button');
    if (form.dataset.busy || !config.published || button.disabled || button.classList.contains('disabled')) { status.textContent = config.published ? 'Please choose an available product option.' : 'This product is a preview and cannot be purchased yet.'; return; }
    if (!form.reportValidity()) return;
    const body = new FormData(form);
    body.delete('add-to-cart'); // Native form handler must not also process this AJAX request.
    body.set('action', 'urban_shisha_product_add_to_cart'); body.set('nonce', config.cartNonce); body.set('product_id', config.productId);
    form.dataset.busy = '1'; button.disabled = true; status.textContent = 'Adding to your bag…';
    try {
      const response = await fetch(config.ajaxUrl, { method: 'POST', body, credentials: 'same-origin' });
      const result = await response.json();
      if (!response.ok || !result.fragments) throw new Error(result.data?.message || 'Could not add this product. Please try again.');
      status.textContent = 'Added to your bag.';
      if (window.jQuery) window.jQuery(document.body).trigger('added_to_cart', [result.fragments, result.cart_hash, window.jQuery(button)]);
      window.UrbanCommerce?.openDrawer('cart');
    } catch (error) { status.textContent = error.message; }
    finally { delete form.dataset.busy; button.disabled = !config.published; }
  }));
  document.getElementById('pdp-mobile-action').addEventListener('click', () => { root.querySelector('.pdp-buy-panel').scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'start' }); });
  if (window.jQuery) {
    window.jQuery(root.querySelector('.variations_form')).on('found_variation', (event, variation) => {
      if (variation.price_html) ['pdp-price', 'pdp-mobile-price'].forEach((id) => { document.getElementById(id).innerHTML = variation.price_html; });
      if (variation.image?.full_src) { gallery = [{ src: variation.image.full_src, name: variation.image.alt || config.gallery[0].name, type: 'photo' }, ...originalGallery]; index = 0; render(); }
      if (!config.published) root.querySelectorAll('.single_add_to_cart_button').forEach((button) => { button.disabled = true; button.textContent = 'Preview only'; });
    }).on('reset_data', () => { gallery = [...originalGallery]; index = 0; render(); ['pdp-price', 'pdp-mobile-price'].forEach((id) => { document.getElementById(id).innerHTML = config.priceHtml; }); });
  }
  render();
  if (window.gsap && !matchMedia('(prefers-reduced-motion: reduce)').matches) window.gsap.from(root.querySelectorAll('.pdp-gallery, .pdp-buy-panel'), { y: 20, opacity: 0, duration: 0.7, stagger: 0.12, ease: 'power2.out', clearProps: 'all' });
})();
