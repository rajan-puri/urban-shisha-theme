/* WooCommerce catalogue + quote list. CF7 submits the real enquiry. */
(()=>{'use strict';
const config=window.UrbanWholesaleConfig||{},products=config.products||[],byId=new Map(products.map(p=>[Number(p.id),p]));
const $=s=>document.querySelector(s),escape=s=>String(s??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const key='urban-wholesale-enquiry-v2',clamp=n=>Math.min(9999,Math.max(1,Math.floor(Number(n)||1)));
let rows=[],category='all',query='',limit=24;
try{const saved=JSON.parse(localStorage.getItem(key)||'[]');if(Array.isArray(saved))rows=saved.filter(r=>r&&byId.has(Number(r.id))).map(r=>({id:Number(r.id),qty:clamp(r.qty)}));}catch{}
const grid=$('#ws-products'),form=$('.ws-form-container .wpcf7-form');if(!grid)return;
// Account for the actual sticky header height and WordPress admin-bar offset.
const header=$('.site-header'),enquiry=$('.ws-enquiry');
function updateStickyOffset(){
 if(!header||!enquiry)return;
 const headerTop=parseFloat(getComputedStyle(header).top)||0;
 enquiry.style.setProperty('--ws-sticky-top',`${Math.ceil(headerTop+header.offsetHeight+28)}px`);
}
if(header&&enquiry){
 new ResizeObserver(updateStickyOffset).observe(header);
 new MutationObserver(updateStickyOffset).observe(header,{attributes:true,attributeFilter:['class']});
 window.addEventListener('resize',updateStickyOffset,{passive:true});
 updateStickyOffset();
}

const more=document.createElement('button');more.type='button';more.className='button ws-load-more';more.textContent='Show more products';grid.after(more);
const cats=[{slug:'all',name:'All products'},...(config.categories||[]).filter(c=>products.some(p=>p.categories.includes(c.slug)))];
$('#ws-categories').innerHTML=cats.map(c=>`<button type="button" data-category="${escape(c.slug)}" aria-pressed="${c.slug===category}">${escape(c.name)}</button>`).join('');
function save(){try{localStorage.setItem(key,JSON.stringify(rows));}catch{}}
function payload(){return rows.map(r=>{const p=byId.get(r.id);return `${p.name} × ${r.qty}\nProduct ID: ${p.id}\n${p.permalink}`;}).join('\n\n');}
function sync(){const input=$('#ws-bulk-items');if(input)input.value=payload();}
function cards(){const list=products.filter(p=>(category==='all'||p.categories.includes(category))&&`${p.name} ${p.brand_name} ${p.category_name}`.toLowerCase().includes(query));
 grid.innerHTML=list.slice(0,limit).map(p=>`<article class="ws-product-card"><a class="ws-card-image" href="${escape(p.permalink)}" aria-label="View ${escape(p.name)}">${p.art_html}</a><div class="ws-card-copy"><span>${escape(p.category_name)}</span><h3><a href="${escape(p.permalink)}">${escape(p.name)}</a></h3><p>Wholesale quote on request${p.is_variable?' · Specify your preferred option in the enquiry':''}</p><button type="button" class="ws-card-add" data-bulk-add="${p.id}" data-selected="${rows.some(r=>r.id===p.id)}">${rows.some(r=>r.id===p.id)?'Add another':'Add to list'} <svg aria-hidden="true"><use href="#i-plus"/></svg></button></div></article>`).join('');
 $('#ws-results-count').textContent=`Showing ${Math.min(limit,list.length)} of ${list.length} products`;$('#ws-no-results').hidden=!!list.length;more.hidden=limit>=list.length;
 document.querySelectorAll('#ws-categories button').forEach(b=>b.setAttribute('aria-pressed',String(b.dataset.category===category)));
}
function list(focus){const units=rows.reduce((n,r)=>n+r.qty,0),summary=rows.length?`${rows.length} products · ${units.toLocaleString('en-IN')} units`:'No products selected yet.';
 $('#ws-enquiry-count').textContent=summary;$('#ws-form-count').textContent=summary;$('#ws-mobile-count').textContent=rows.length?`${units} units in your list`:'Your bulk list';$('#ws-enquiry-empty').hidden=!!rows.length;$('#ws-clear-list').hidden=!rows.length;
 $('#ws-enquiry-items').innerHTML=rows.map(r=>{const p=byId.get(r.id);return `<article class="ws-line"><div class="ws-line-art">${p.art_html}</div><div class="ws-line-copy"><h3>${escape(p.name)}</h3><div class="ws-line-actions"><div class="ws-line-qty"><button type="button" data-step="-1" data-id="${r.id}" aria-label="Decrease quantity" ${r.qty===1?'disabled':''}>−</button><input type="number" data-qty="${r.id}" min="1" max="9999" value="${r.qty}" aria-label="Quantity for ${escape(p.name)}"><button type="button" data-step="1" data-id="${r.id}" aria-label="Increase quantity" ${r.qty===9999?'disabled':''}>+</button></div><button type="button" class="ws-remove" data-remove="${r.id}">Remove</button></div></div></article>`;}).join('');
 if(rows.length)$('#ws-selection-error').hidden=true;sync();save();document.querySelectorAll('[data-bulk-add]').forEach(b=>{const selected=rows.some(r=>r.id===Number(b.dataset.bulkAdd));b.dataset.selected=String(selected);b.firstChild.textContent=selected?'Add another ':'Add to list ';});if(focus)$(focus)?.focus({preventScroll:true});
}
$('#ws-categories').addEventListener('click',e=>{const b=e.target.closest('[data-category]');if(!b)return;category=b.dataset.category;limit=24;cards();});
$('#ws-search').addEventListener('input',e=>{query=e.target.value.trim().toLowerCase();limit=24;cards();});more.addEventListener('click',()=>{limit+=24;cards();});
$('#ws-reset-search').addEventListener('click',()=>{category='all';query='';limit=24;$('#ws-search').value='';cards();});
grid.addEventListener('click',e=>{const b=e.target.closest('[data-bulk-add]');if(!b)return;const id=Number(b.dataset.bulkAdd),row=rows.find(r=>r.id===id);if(row)row.qty=clamp(row.qty+1);else rows.push({id,qty:1});list();});
$('#ws-enquiry-items').addEventListener('click',e=>{const b=e.target.closest('button');if(!b)return;if(b.dataset.remove){rows=rows.filter(r=>r.id!==Number(b.dataset.remove));list();$('#ws-enquiry-title').focus({preventScroll:true});}else if(b.dataset.step){const row=rows.find(r=>r.id===Number(b.dataset.id));if(row)row.qty=clamp(row.qty+Number(b.dataset.step));list(`[data-step="${b.dataset.step}"][data-id="${b.dataset.id}"]:not([disabled])`);}});
$('#ws-enquiry-items').addEventListener('change',e=>{if(!e.target.dataset.qty)return;const row=rows.find(r=>r.id===Number(e.target.dataset.qty));if(row)row.qty=clamp(e.target.value);list(`[data-qty="${e.target.dataset.qty}"]`);});
$('#ws-clear-list').addEventListener('click',()=>{rows=[];list();$('#ws-enquiry-title').focus({preventScroll:true});});
form?.addEventListener('submit',e=>{sync();if(!rows.length){e.preventDefault();e.stopImmediatePropagation();$('#ws-selection-error').hidden=false;$('#ws-enquiry-title').focus();}},true);
document.addEventListener('wpcf7mailsent',e=>{if(!e.target.closest('.ws-form-container'))return;rows=[];list();});document.addEventListener('wpcf7reset',sync);
cards();list();if(window.gsap&&!matchMedia('(prefers-reduced-motion: reduce)').matches)gsap.from('.ws-hero-copy',{opacity:0,y:20,duration:.7,ease:'power2.out'});
})();
