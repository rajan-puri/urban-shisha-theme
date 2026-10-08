/* WordPress shell only. Page-specific preview commerce scripts are not loaded here. */
(()=>{
'use strict';
const $=(s,r=document)=>r.querySelector(s),$$=(s,r=document)=>[...r.querySelectorAll(s)];
const context=window.UrbanShishaTheme||{},catalog=window.UrbanCatalog;
const icon=n=>`<svg aria-hidden="true"><use href="#i-${n}"/></svg>`;
let toastTimer,megaTimer;
function notify(text){const toast=$('#toast');if(!toast)return;toast.textContent=text;toast.classList.add('visible');clearTimeout(toastTimer);toastTimer=setTimeout(()=>toast.classList.remove('visible'),3000);}
function loadPhoto(el){const img=$('image[data-photo-src]',el);if(img){img.setAttribute('href',img.dataset.photoSrc);delete img.dataset.photoSrc;}}
const observer='IntersectionObserver'in window?new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){loadPhoto(e.target);observer.unobserve(e.target);}}),{rootMargin:'350px'}):null;
if(catalog&&context.assetBase)$$('.product-art').forEach(el=>{const p=catalog.products.find(p=>el.classList.contains('art-'+p.art));if(!p)return;const [x,y,w,h,sw,sh]=catalog.imageFrames[p.art];const src=new URL(`images/product-cutout-${p.art}.webp`,context.assetBase).href;el.innerHTML=`<svg class="catalog-photo" viewBox="${x} ${y} ${w} ${h}" preserveAspectRatio="xMidYMid meet" aria-hidden="true"><image data-photo-src="${src}" width="${sw}" height="${sh}"/></svg>`;if(observer)observer.observe(el);else loadPhoto(el);});
const header=$('.site-header'),toggle=$('#mega-toggle'),mega=$('#category-menu'),menu=$('.mobile-toggle'),mobile=$('#mobile-nav');
if(header&&toggle&&mega&&menu&&mobile){
 function closeMega(){clearTimeout(megaTimer);mega.hidden=true;toggle.setAttribute('aria-expanded','false');}
 function openMega(){clearTimeout(megaTimer);mega.hidden=false;toggle.setAttribute('aria-expanded','true');$$('.product-art',mega).forEach(loadPhoto);}
 function closeMobile(){mobile.hidden=true;menu.setAttribute('aria-expanded','false');menu.setAttribute('aria-label','Open navigation');menu.innerHTML=icon('menu');}
 toggle.addEventListener('click',()=>mega.hidden?openMega():closeMega());
 toggle.addEventListener('keydown',e=>{if(e.key==='ArrowDown'){e.preventDefault();openMega();$('a',mega)?.focus();}});
 toggle.addEventListener('pointerenter',e=>{if(e.pointerType==='mouse'&&matchMedia('(min-width:901px)').matches)openMega();});
 mega.addEventListener('pointerenter',()=>clearTimeout(megaTimer));header.addEventListener('pointerleave',()=>megaTimer=setTimeout(closeMega,180));header.addEventListener('focusout',e=>{if(!header.contains(e.relatedTarget))closeMega();});
 menu.addEventListener('click',()=>{const show=mobile.hidden;mobile.hidden=!show;menu.setAttribute('aria-expanded',String(show));menu.setAttribute('aria-label',show?'Close navigation':'Open navigation');menu.innerHTML=icon(show?'close':'menu');if(show)$$('.product-art',mobile).forEach(loadPhoto);});
 document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(!mobile.hidden){closeMobile();menu.focus();}if(!mega.hidden){closeMega();toggle.focus();}}});document.addEventListener('click',e=>{if(!header.contains(e.target))closeMega();});matchMedia('(max-width:900px)').addEventListener('change',()=>{closeMega();closeMobile();});
 let queued=false;window.addEventListener('scroll',()=>{if(queued)return;queued=true;requestAnimationFrame(()=>{header.classList.toggle('is-scrolled',scrollY>30);queued=false;});},{passive:true});
}
const footerMQ=matchMedia('(max-width:600px)');function footerLayout(){$$('.footer-toggle').forEach((button,i)=>{const expanded=!footerMQ.matches||i===0,target=$('#'+button.getAttribute('aria-controls'));button.setAttribute('aria-expanded',String(expanded));button.tabIndex=footerMQ.matches?0:-1;if(target)target.hidden=!expanded;});}footerLayout();footerMQ.addEventListener('change',footerLayout);
document.addEventListener('click',e=>{const button=e.target.closest('button');if(!button)return;
 if(button.matches('.footer-toggle')&&footerMQ.matches){const target=$('#'+button.getAttribute('aria-controls')),show=button.getAttribute('aria-expanded')!=='true';button.setAttribute('aria-expanded',String(show));if(target)target.hidden=!show;return;}
 if(button.dataset.open==='search'&&context.shopUrl){const url=new URL(context.shopUrl);url.searchParams.set('search','');location.href=url.href;return;}
 if((button.dataset.info==='contact'||button.dataset.info==='faq')&&context.contactUrl){location.href=context.contactUrl;return;}
 if(button.dataset.info==='social'){const url=window.URBAN_STORE?.instagram||'';if(/^https:\/\/(www\.)?instagram\.com\//.test(url))window.open(url,'_blank','noopener,noreferrer');else notify('Instagram details will be added when available.');}
});
$('#newsletter-form')?.addEventListener('submit',e=>{e.preventDefault();const note=$('#newsletter-note');if(note)note.textContent='Subscriptions are not connected yet. Your email has not been submitted.';notify('Subscriptions will open with the store.');});
const age=$('#age-dialog');if(age){function lock(){document.body.classList.toggle('is-locked',!!$('dialog[open]'));}age.addEventListener('cancel',e=>e.preventDefault());let accepted=false;try{accepted=localStorage.getItem('urban-preview-age')==='accepted';}catch{}if(context.requireAgeConfirmation&&!accepted){age.showModal();lock();}$('#age-accept')?.addEventListener('click',()=>{try{localStorage.setItem('urban-preview-age','accepted');}catch{}age.close();lock();});$('#age-decline')?.addEventListener('click',()=>{const actions=$('.age-actions'),declined=$('#age-declined');if(actions)actions.hidden=true;if(declined)declined.hidden=false;});}
})();
