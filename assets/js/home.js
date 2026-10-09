/* Real WooCommerce homepage interactions; original design and motion are retained. */
(() => {
'use strict';
const $ = (s,r=document) => r.querySelector(s);
const $$ = (s,r=document) => [...r.querySelectorAll(s)];
const config = window.UrbanHomeConfig || {};
const reduced = () => matchMedia('(prefers-reduced-motion: reduce)').matches;
const notify = text => window.UrbanCommerce?.showToast(text);
const jump = id => $(id)?.scrollIntoView({behavior:reduced()?'instant':'smooth'});
const refresh = () => window.ScrollTrigger?.refresh();

function emptyState(grid) {
 if(!grid)return;
 let note=grid.nextElementSibling;
 if(!note?.classList.contains('home-empty')){note=document.createElement('p');note.className='home-empty';note.setAttribute('role','status');note.textContent='No products in this selection yet.';grid.after(note);}
 note.hidden=$$('.product-card:not([data-clone])',grid).some(card=>!card.hidden);
}

let hookahLoopCleanup = null;
function initHookahLoop() {
 if (hookahLoopCleanup) {
  hookahLoopCleanup();
  hookahLoopCleanup = null;
 }
 const rail = $('#hookah-products');
 if (!rail) return;

 $$('[data-clone="1"]', rail).forEach(el => el.remove());

 const originalCards = $$('.product-card:not([hidden])', rail);
 if (originalCards.length <= 2) {
  return;
 }

 const cloneCount = Math.min(originalCards.length, 4);
 const makeClone = card => {
  const clone = card.cloneNode(true);
  clone.removeAttribute('id');
  clone.dataset.clone = '1';
  clone.setAttribute('aria-hidden', 'true');
  clone.setAttribute('tabindex', '-1');
  $$('a, button, input', clone).forEach(el => {
   el.removeAttribute('id');
   el.setAttribute('tabindex', '-1');
   el.setAttribute('aria-hidden', 'true');
  });
  return clone;
 };

 const headClones = originalCards.slice(0, cloneCount).map(makeClone);
 const tailClones = originalCards.slice(-cloneCount).map(makeClone);

 tailClones.reverse().forEach(c => rail.prepend(c));
 headClones.forEach(c => rail.append(c));

 const getGeometry = () => {
  const firstOrig = originalCards[0];
  const lastOrig = originalCards[originalCards.length - 1];
  if (!firstOrig || !lastOrig) return { lead: 0, loop: 0 };
  const firstChild = rail.firstElementChild;
  const lead = firstOrig.offsetLeft - firstChild.offsetLeft;
  const cardGap = originalCards.length > 1
   ? Math.max(0, originalCards[1].offsetLeft - (originalCards[0].offsetLeft + originalCards[0].offsetWidth))
   : 18;
  const loop = (lastOrig.offsetLeft + lastOrig.offsetWidth + cardGap) - firstOrig.offsetLeft;
  return { lead, loop };
 };

 let { lead, loop } = getGeometry();
 if (rail.scrollLeft < lead * 0.5) {
  rail.scrollLeft = lead;
 }

 let isWrapping = false;
 const onScroll = () => {
  if (isWrapping) return;
  const { lead: curLead, loop: curLoop } = getGeometry();
  if (curLoop <= 0) return;
  const sl = rail.scrollLeft;
  if (sl >= curLead + curLoop) {
   isWrapping = true;
   rail.scrollLeft = sl - curLoop;
   isWrapping = false;
  } else if (sl < curLead - 5) {
   isWrapping = true;
   rail.scrollLeft = sl + curLoop;
   isWrapping = false;
  }
 };

 rail.addEventListener('scroll', onScroll, { passive: true });

 const onArrowClick = e => {
  const b = e.target.closest('[data-rail]');
  if (!b) return;
  const dir = Number(b.dataset.rail) || 1;
  const { lead: curLead, loop: curLoop } = getGeometry();
  if (curLoop <= 0) {
   rail.scrollBy({ left: dir * (rail.clientWidth * 0.75), behavior: reduced() ? 'instant' : 'smooth' });
   return;
  }
  const step = rail.clientWidth * 0.75;
  const sl = rail.scrollLeft;
  if (dir > 0 && sl + step >= curLead + curLoop) {
   rail.scrollLeft = sl - curLoop;
  } else if (dir < 0 && sl - step < curLead - 5) {
   rail.scrollLeft = sl + curLoop;
  }
  rail.scrollBy({ left: dir * step, behavior: reduced() ? 'instant' : 'smooth' });
 };
 document.addEventListener('click', onArrowClick);

 let drag = null, moved = false;
 const onPointerDown = e => {
  if (e.pointerType !== 'mouse' || e.button !== 0 || e.target.closest('.save-product, .add-product, .quick-add')) return;
  e.preventDefault();
  drag = { x: e.clientX, scroll: rail.scrollLeft };
  moved = false;
 };
 const onPointerMove = e => {
  if (!drag) return;
  const dx = e.clientX - drag.x;
  if (Math.abs(dx) > 5) {
   moved = true;
   rail.classList.add('is-dragging');
   rail.scrollLeft = drag.scroll - dx;
   const { lead: curLead, loop: curLoop } = getGeometry();
   if (curLoop > 0) {
    if (rail.scrollLeft >= curLead + curLoop) {
     rail.scrollLeft -= curLoop;
     drag.scroll -= curLoop;
    } else if (rail.scrollLeft < curLead - 5) {
     rail.scrollLeft += curLoop;
     drag.scroll += curLoop;
    }
   }
  }
 };
 const onPointerUp = () => {
  if (!drag) return;
  drag = null;
  rail.classList.remove('is-dragging');
 };
 const onClickCapture = e => {
  if (moved) {
   e.preventDefault();
   e.stopPropagation();
   moved = false;
  }
 };

 rail.addEventListener('pointerdown', onPointerDown);
 window.addEventListener('pointermove', onPointerMove);
 window.addEventListener('pointerup', onPointerUp);
 window.addEventListener('pointercancel', onPointerUp);
 rail.addEventListener('click', onClickCapture, true);

 hookahLoopCleanup = () => {
  rail.removeEventListener('scroll', onScroll);
  document.removeEventListener('click', onArrowClick);
  rail.removeEventListener('pointerdown', onPointerDown);
  window.removeEventListener('pointermove', onPointerMove);
  window.removeEventListener('pointerup', onPointerUp);
  window.removeEventListener('pointercancel', onPointerUp);
  rail.removeEventListener('click', onClickCapture, true);
  $$('[data-clone="1"]', rail).forEach(el => el.remove());
 };
}

function budgetFilter(key) {
 const range = config.budgetRanges?.[key];
 $$('[data-budget]').forEach(b => {
  b.classList.toggle('is-active', b.dataset.budget === key);
  b.setAttribute('aria-pressed', String(b.dataset.budget === key));
 });
 const grid = $('#hookah-products');
 $$('.product-card:not([data-clone])', grid || document.createElement('div')).forEach(card => {
  const value = card.dataset.price;
  const price = Number(value);
  card.hidden = !!range && (value === '' || price < range.min || (range.max !== null && price > range.max));
 });
 emptyState(grid);
 initHookahLoop();
 refresh();
}

function accessoryFilter(key) {
 const grid = $('#accessory-products');
 $$('[data-accessory]').forEach(b => {
  const isActive = b.dataset.accessory === key;
  b.classList.toggle('is-active', isActive);
  b.setAttribute('aria-pressed', String(isActive));
 });
 $$('.product-card', grid || document.createElement('div')).forEach(card => {
  const tabs = (card.dataset.accessoryTabs || card.dataset.categories || '').split(' ');
  card.hidden = !tabs.includes(key);
 });
 const activeBtn = $(`[data-accessory="${key}"]`);
 const viewAll = $('#accessory-view-all');
 if (viewAll && activeBtn?.dataset.archiveUrl) {
  viewAll.href = activeBtn.dataset.archiveUrl;
  const label = $('.view-all-label', viewAll);
  if (label && activeBtn.dataset.btnLabel) {
   label.textContent = activeBtn.dataset.btnLabel;
  }
 }
 emptyState(grid);
 refresh();
}

$$('[data-budget]').forEach(b => b.addEventListener('click', () => budgetFilter(b.dataset.budget)));
$$('[data-accessory]').forEach(b => b.addEventListener('click', () => accessoryFilter(b.dataset.accessory)));
$$('[data-category],[data-nav-category]').forEach(b => b.addEventListener('click', e => {
 if (b.tagName === 'A' && b.getAttribute('href') !== '#accessories') return;
 e.preventDefault();
 accessoryFilter(b.dataset.category || b.dataset.navCategory);
 jump('#accessories');
}));

const choices = $$('.spotlight-choice');
let selected = 0;
function spotlight(index, animate = true) {
 if (!choices.length) return;
 selected = (index + choices.length) % choices.length;
 const choice = choices[selected], image = $('#spotlight-image'), copy = $('#spotlight-copy'), cta = $('#spotlight-add');
 choices.forEach((b, i) => b.setAttribute('aria-pressed', String(i === selected)));
 const name = $('.spotlight-choice-copy strong', choice)?.textContent || '';
 if (image) {
  image.replaceChildren($('.product-art', choice).cloneNode(true));
  image.href = choice.dataset.productUrl;
  image.setAttribute('aria-label', 'View ' + name);
 }
 if (copy) {
  const p = document.createElement('p'), h = document.createElement('h3'), price = document.createElement('strong');
  p.textContent = $('.spotlight-choice-tag', choice)?.textContent || '';
  h.textContent = name;
  price.append($('.spotlight-choice-copy>span', choice).cloneNode(true));
  copy.replaceChildren(p, h, price);
 }
 if (cta) {
  cta.href = choice.dataset.productUrl;
  cta.removeAttribute('data-add');
  if (choice.dataset.quickAdd === '1') cta.dataset.add = choice.dataset.productId;
  cta.replaceChildren(document.createTextNode(choice.dataset.quickAdd === '1' ? 'Add to bag ' : 'View product '));
  const arrow = document.createElementNS('http://www.w3.org/2000/svg', 'svg'), use = document.createElementNS('http://www.w3.org/2000/svg', 'use');
  arrow.setAttribute('aria-hidden', 'true');
  use.setAttribute('href', '#i-arrow');
  arrow.append(use);
  cta.append(arrow);
 }
 const position = $('#spotlight-position');
 if (position) position.textContent = String(selected + 1).padStart(2, '0') + ' / ' + String(choices.length).padStart(2, '0');
 const status = $('#spotlight-status');
 if (status) status.textContent = 'Showing ' + name;
 if (animate && window.gsap && !reduced()) gsap.fromTo([image, copy].filter(Boolean), { x: 12, opacity: .4 }, { x: 0, opacity: 1, duration: .35, ease: 'power2.out', clearProps: 'opacity,transform' });
}
choices.forEach((b, i) => b.addEventListener('click', () => spotlight(i)));
$('#spotlight-prev')?.addEventListener('click', () => spotlight(selected - 1));
$('#spotlight-next')?.addEventListener('click', () => spotlight(selected + 1));

const form = $('#setup-form');
const personalised = new Set();
const currency = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 2 });
function builder(animate = false) {
 if (!form) return;
 let total = 0, complete = true, available = true;
 ['hookah', 'bowl', 'heat', 'charcoal'].forEach(key => {
  const select = $('#setup-' + key), option = select?.selectedOptions[0], art = $('#builder-' + key);
  if (!option || option.dataset.price === '') { complete = false; } else total += Number(option.dataset.price);
  if (option?.dataset.available !== '1') available = false;
  const template = $('template[data-builder-art="' + (option?.value || '') + '"]', form);
  if (art) {
   art.replaceChildren(...(template ? [template.content.cloneNode(true)] : []));
   art.className = (key === 'hookah' ? 'builder-hookah ' : '') + 'product-art has-cutout' + (option?.dataset.art >= 0 ? ' art-' + option.dataset.art : '');
   art.setAttribute('aria-label', option?.dataset.name || 'No product selected');
  }
 });
 const totalNode = $('#setup-total');
 if (totalNode) totalNode.textContent = complete ? currency.format(total) : 'Price on request';
 $('button[type="submit"]', form).disabled = !complete || !available;
 const status = $('#builder-availability');
 if (status) status.textContent = available ? 'All selected pieces are available.' : 'One or more selected pieces are currently unavailable.';
 if ($('#vibe-label')) $('#vibe-label').textContent = personalised.size + ' / 4 personalised';
 if ($('#vibe-fill')) $('#vibe-fill').style.transform = 'scaleX(' + (personalised.size / 4) + ')';
 $('.vibe-meter', form)?.setAttribute('aria-valuenow', String(personalised.size));
 if (animate && window.gsap && !reduced()) gsap.fromTo($$('.builder-stage .product-art'), { y: 8 }, { y: 0, duration: .3, clearProps: 'transform' });
}
form?.addEventListener('change', e => {
 if (e.target.matches('select')) {
  personalised.add(e.target.id);
  builder(true);
 }
 });
builder();

async function post(action, values, nonce) {
 const data = new FormData();
 data.set('action', action);
 data.set('nonce', nonce || '');
 Object.entries(values).forEach(([k, v]) => data.set(k, v));
 const response = await fetch(config.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' });
 const result = await response.json();
 if (result.success === false || !response.ok) throw Error(result.data?.message || 'This selection could not be added.');
 if (window.jQuery) { jQuery(document.body).trigger('wc_fragment_refresh'); }
 window.UrbanCommerce?.openDrawer('cart');
 return result;
}

form?.addEventListener('submit', async e => {
 e.preventDefault();
 const b = $('button[type="submit"]', form);
 if (b.disabled) return;
 b.disabled = true;
 try {
  const ids = ['hookah', 'bowl', 'heat', 'charcoal'].map(k => $('#setup-' + k).value);
  const result = await post('urban_shisha_builder_add_to_cart', { product_ids: ids.join(',') }, config.homeNonce);
  notify(result.data.message);
 } catch (error) {
  notify(error.message);
 } finally {
  builder();
 }
});

document.addEventListener('click', async e => {
 const b = e.target.closest('[data-add]');
 if (!b || b.closest('#setup-form')) return;
 e.preventDefault();
 if (b.dataset.busy) return;
 b.dataset.busy = '1';
 try {
  await post('urban_shisha_add_to_cart', { product_id: b.dataset.add, quantity: '1' }, config.cartNonce);
  notify('Added to your bag.');
  window.UrbanMotion?.added(b);
 } catch (error) {
  notify(error.message);
 } finally {
  delete b.dataset.busy;
 }
});

const dialog = $('#content-dialog'), body = $('#content-body');
$$('button[data-guide]').forEach(b => b.addEventListener('click', () => {
 const template = $('.guide-dialog-template', b);
 if (!template || !dialog || !body) return;
 body.replaceChildren(template.content.cloneNode(true));
 dialog.showModal();
 document.body.classList.add('is-locked');
}));
dialog?.addEventListener('click', e => {
 if (e.target.closest('[data-close-guide]')) {
  dialog.close();
  jump('#collection');
 }
});
dialog?.addEventListener('close', () => document.body.classList.remove('is-locked'));

emptyState($('#hookah-products'));
emptyState($('#accessory-products'));
initHookahLoop();

if (!reduced() && window.gsap && window.ScrollTrigger && window.UrbanMotion) {
 UrbanMotion.start({ ribbonTemplate: $('.ticker-copy')?.innerHTML || '' });
}
})();
