/* WordPress shell only. Page-specific preview commerce scripts are not loaded here. */
(()=>{
'use strict';
const $=(s,r=document)=>r.querySelector(s),$$=(s,r=document)=>[...r.querySelectorAll(s)];
const context=window.UrbanShishaTheme||{};
const icon=n=>`<svg aria-hidden="true"><use href="#i-${n}"/></svg>`;
let toastTimer,megaTimer;
function notify(text){const toast=$('#toast');if(!toast)return;toast.textContent=text;toast.classList.add('visible');clearTimeout(toastTimer);toastTimer=setTimeout(()=>toast.classList.remove('visible'),3000);}
const header=$('.site-header'),toggle=$('#mega-toggle'),mega=$('#category-menu'),menu=$('.mobile-toggle'),mobile=$('#mobile-nav');
function closeMega(restore=false){clearTimeout(megaTimer);if(mega)mega.hidden=true;toggle?.setAttribute('aria-expanded','false');if(restore)toggle?.focus();}
function closeMobile(restore=false){if(mobile)mobile.hidden=true;menu?.setAttribute('aria-expanded','false');menu?.setAttribute('aria-label','Open navigation');if(menu)menu.innerHTML=icon('menu');if(restore)menu?.focus();}
function closeAccount(){const account=$('#account-trigger[aria-expanded="true"]');account?.click();}
if(toggle&&mega){
 function openMega(){clearTimeout(megaTimer);closeMobile();closeAccount();mega.hidden=false;toggle.setAttribute('aria-expanded','true');}
 toggle.addEventListener('click',()=>mega.hidden?openMega():closeMega());
 toggle.addEventListener('keydown',e=>{if(e.key==='ArrowDown'){e.preventDefault();openMega();$('a',mega)?.focus();}});
 toggle.parentElement.addEventListener('pointerenter',e=>{if(e.pointerType==='mouse'&&matchMedia('(min-width:901px)').matches)openMega();});
 mega.addEventListener('pointerenter',()=>clearTimeout(megaTimer));
 header?.addEventListener('pointerleave',()=>megaTimer=setTimeout(()=>{if(!mega.contains(document.activeElement)&&document.activeElement!==toggle)closeMega();},180));
}
if(menu&&mobile){
 menu.addEventListener('click',()=>{const show=mobile.hidden;closeMega();if(show)closeAccount();mobile.hidden=!show;menu.setAttribute('aria-expanded',String(show));menu.setAttribute('aria-label',show?'Close navigation':'Open navigation');menu.innerHTML=icon(show?'close':'menu');});
 mobile.addEventListener('click',e=>{if(e.target.closest('a'))closeMobile();});
}
for(const button of $$('.nav-submenu-toggle')){
 const panel=document.getElementById(button.getAttribute('aria-controls')),entry=button.parentElement;
 if(!panel)continue;
 const set=show=>{panel.hidden=!show;button.setAttribute('aria-expanded',String(show));};
 button.addEventListener('click',()=>{closeMega();set(panel.hidden);});
 button.addEventListener('keydown',e=>{if(e.key==='ArrowDown'){e.preventDefault();set(true);$('a',panel)?.focus();}});
 entry.addEventListener('pointerenter',e=>{if(e.pointerType==='mouse'&&entry.closest('.desktop-nav')){closeMega();set(true);}});
 entry.addEventListener('pointerleave',e=>{if(e.pointerType==='mouse'&&!entry.contains(document.activeElement))set(false);});
 entry.addEventListener('focusout',e=>{if(!entry.contains(e.relatedTarget))set(false);});
 panel.addEventListener('keydown',e=>{if(e.key==='Escape'){e.preventDefault();e.stopPropagation();set(false);button.focus();}});
}
header?.addEventListener('focusout',e=>{if(!header.contains(e.relatedTarget))closeMega();});
document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(mobile&&!mobile.hidden)closeMobile(true);else if(mega&&!mega.hidden)closeMega(true);}});
document.addEventListener('click',e=>{if(header&&!e.composedPath().includes(header)){closeMega();closeMobile();}});
matchMedia('(max-width:900px)').addEventListener('change',()=>{closeMega();closeMobile();$$('.nav-submenu-toggle').forEach(b=>{b.setAttribute('aria-expanded','false');const p=document.getElementById(b.getAttribute('aria-controls'));if(p)p.hidden=true;});});
if(header){let queued=false;const state=()=>header.classList.toggle('is-scrolled',scrollY>30);state();window.addEventListener('scroll',()=>{if(queued)return;queued=true;requestAnimationFrame(()=>{state();queued=false;});},{passive:true});}
const search=$('#header-search');let searchTrigger;
if(search){
 function closeSearch(){search.close();document.body.classList.toggle('is-locked',!!$('dialog[open]'));searchTrigger?.focus();}
 $$('[data-open="search"]').forEach(button=>button.addEventListener('click',()=>{searchTrigger=button;closeMega();closeMobile();closeAccount();search.showModal();document.body.classList.add('is-locked');$('#header-search-input')?.focus();}));
 $('[data-close-search]',search)?.addEventListener('click',closeSearch);
 search.addEventListener('cancel',e=>{e.preventDefault();closeSearch();});
 search.addEventListener('click',e=>{const r=search.getBoundingClientRect();if(e.target===search&&(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom))closeSearch();});
}
const footerMQ=matchMedia('(max-width:600px)');function footerLayout(){$$('.footer-toggle').forEach((button,i)=>{const expanded=!footerMQ.matches||i===0,target=$('#'+button.getAttribute('aria-controls'));button.setAttribute('aria-expanded',String(expanded));button.tabIndex=footerMQ.matches?0:-1;if(target)target.hidden=!expanded;});}footerLayout();footerMQ.addEventListener('change',footerLayout);
document.addEventListener('click',e=>{
 const wlLink=e.target.closest('a.open-wishlist, a.urban-wishlist-trigger, a.menu-item-wishlist');
 if(wlLink&&!e.defaultPrevented){
  const drawer=$('#shop-dialog');
  if(drawer){
   e.preventDefault();
   const c=$('#drawer-panel-cart'),w=$('#drawer-panel-wishlist');
   const eb=$('#drawer-eyebrow'),dt=$('#drawer-title');
   if(eb)eb.textContent='MAKE IT YOURS.';
   if(dt)dt.textContent='Your saved edit.';
   if(c)c.hidden=true;
   if(w)w.hidden=false;
   if(!drawer.open){drawer.showModal();document.body.classList.add('is-locked');}
  }
  return;
 }
 const button=e.target.closest('button');if(!button)return;
 if(button.matches('.footer-toggle')&&footerMQ.matches){const target=$('#'+button.getAttribute('aria-controls')),show=button.getAttribute('aria-expanded')!=='true';button.setAttribute('aria-expanded',String(show));if(target)target.hidden=!show;return;}
 if((button.dataset.info==='contact'||button.dataset.info==='faq')&&context.contactUrl){location.href=context.contactUrl;return;}
 if(button.dataset.info==='social'){const url=window.URBAN_STORE?.instagram||'';if(/^https:\/\/(www\.)?instagram\.com\//.test(url))window.open(url,'_blank','noopener,noreferrer');else notify('Instagram details will be added when available.');}
});
const nlForm=$('#newsletter-form');
if(nlForm){
 nlForm.addEventListener('submit',async e=>{
  e.preventDefault();
  const input=$('#newsletter-email',nlForm),note=$('#newsletter-note',nlForm)||$('#newsletter-note'),btn=$('button[type="submit"]',nlForm);
  const email=input?input.value.trim():'';
  if(!email){if(note){note.textContent='Please enter a valid email address.';note.className='newsletter-note is-error';}return;}
  const ajaxUrl=context.ajaxUrl||'/wp-admin/admin-ajax.php';
  const nonce=nlForm.querySelector('input[name="nonce"]')?.value||context.newsletterNonce||'';
  const fd=new FormData();fd.append('action','urban_shisha_newsletter');fd.append('nonce',nonce);fd.append('email',email);
  if(btn)btn.disabled=true;
  if(note){note.textContent='Subscribing...';note.className='newsletter-note';}
  try{
   const res=await fetch(ajaxUrl,{method:'POST',body:fd,credentials:'same-origin'});
   const data=await res.json();
   if(data.success){
    const msg=data.data?.message||'Thank you for subscribing! Your email has been saved.';
    if(note){note.textContent=msg;note.className='newsletter-note is-success';}
    notify(msg);
    if(input&&data.data?.status==='subscribed')input.value='';
   }else{
    const err=data.data?.message||'Could not subscribe. Please try again.';
    if(note){note.textContent=err;note.className='newsletter-note is-error';}
    notify(err);
   }
  }catch(_){
   const netErr='Network error. Please try again later.';
   if(note){note.textContent=netErr;note.className='newsletter-note is-error';}
   notify(netErr);
  }finally{
   if(btn)btn.disabled=false;
  }
 });
}
const age=$('#age-dialog');if(age){function lock(){document.body.classList.toggle('is-locked',!!$('dialog[open]'));}age.addEventListener('cancel',e=>e.preventDefault());let accepted=false;try{accepted=localStorage.getItem('urban-preview-age')==='accepted';}catch{}if(context.requireAgeConfirmation&&!accepted){age.showModal();lock();}$('#age-accept')?.addEventListener('click',()=>{try{localStorage.setItem('urban-preview-age','accepted');}catch{}age.close();lock();});$('#age-decline')?.addEventListener('click',()=>{const actions=$('.age-actions'),declined=$('#age-declined');if(actions)actions.hidden=true;if(declined)declined.hidden=false;});}
})();
