/* Image-based builder choices enhance real select controls without replacing commerce. */
(() => {
'use strict';
const form=document.querySelector('#setup-form');if(!form)return;
const currency=new Intl.NumberFormat('en-IN',{style:'currency',currency:'INR',maximumFractionDigits:2});
const pickers=[];
const node=(tag,className,text)=>{const e=document.createElement(tag);e.className=className;if(text!==undefined)e.textContent=text;return e;};
for(const select of form.querySelectorAll('select[id^="setup-"]')){
 const field=select.closest('.builder-field');if(!field)continue;
 const picker=node('div','builder-picker');const trigger=node('button','builder-picker-trigger');trigger.type='button';
 trigger.id=select.id+'-trigger';trigger.setAttribute('aria-haspopup','dialog');trigger.setAttribute('aria-expanded','false');
 const panel=node('div','builder-picker-panel');panel.id=select.id+'-choices';panel.hidden=true;panel.setAttribute('role','dialog');panel.setAttribute('aria-label','Choose '+select.getAttribute('aria-label'));trigger.setAttribute('aria-controls',panel.id);
 const search=node('input','builder-picker-search');search.type='search';search.placeholder='Search '+select.getAttribute('aria-label').toLowerCase()+' products';search.setAttribute('aria-label',search.placeholder);
 const list=node('div','builder-picker-list');const empty=node('p','builder-picker-empty','No matching products.');empty.hidden=true;
 const image=option=>{const thumb=node('span','builder-picker-thumb');const template=[...form.querySelectorAll('template[data-builder-art]')].find(t=>t.dataset.builderArt===option.value);if(template)thumb.append(template.content.cloneNode(true));thumb.setAttribute('aria-hidden','true');return thumb;};
 const price=option=>option.dataset.price===''?'Price on request':(option.dataset.variable==='1'?'From ':'')+currency.format(Number(option.dataset.price));
 const rows=[...select.options].map(option=>{
  const row=node('button','builder-picker-option');row.type='button';row.dataset.value=option.value;row.setAttribute('aria-pressed',String(option.selected));
  const copy=node('span','builder-picker-copy');copy.append(node('span','builder-picker-name',option.dataset.name),node('span','builder-picker-price',price(option)));
  if(option.dataset.available!=='1')copy.append(node('span','builder-picker-status','Currently unavailable'));
  row.append(image(option),copy,node('span','builder-picker-check','✓'));
  row.addEventListener('click',()=>{select.value=option.value;select.dispatchEvent(new Event('change',{bubbles:true}));sync();close(true);});list.append(row);return row;
 });
 const filter=()=>{const q=search.value.trim().toLocaleLowerCase();rows.forEach(row=>{row.hidden=!row.textContent.toLocaleLowerCase().includes(q);});empty.hidden=rows.some(row=>!row.hidden);};
 const sync=()=>{const option=select.selectedOptions[0];trigger.replaceChildren();if(!option){trigger.textContent='No products available';trigger.disabled=true;return;}
  const copy=node('span','builder-picker-copy');copy.append(node('span','builder-picker-name',option.dataset.name),node('span','builder-picker-price',price(option)));trigger.append(image(option),copy,node('span','builder-picker-chevron','⌄'));
  trigger.setAttribute('aria-label','Choose '+select.getAttribute('aria-label')+': '+option.dataset.name);rows.forEach(row=>row.setAttribute('aria-pressed',String(row.dataset.value===select.value)));
 };
 const close=(focus=false)=>{panel.hidden=true;trigger.setAttribute('aria-expanded','false');if(focus)trigger.focus();};
 trigger.addEventListener('click',()=>{if(!panel.hidden){close();return;}pickers.forEach(p=>p.close());search.value='';filter();panel.hidden=false;trigger.setAttribute('aria-expanded','true');list.scrollTop=0;search.focus({preventScroll:true});});
 search.addEventListener('input',filter);select.addEventListener('change',sync);
 picker.addEventListener('keydown',e=>{if(e.key==='Escape'&&!panel.hidden){e.preventDefault();e.stopPropagation();close(true);}if(!panel.hidden&&['ArrowDown','ArrowUp','Home','End'].includes(e.key)){const visible=rows.filter(row=>!row.hidden);if(!visible.length)return;e.preventDefault();const i=visible.indexOf(document.activeElement);const next=e.key==='Home'?0:e.key==='End'?visible.length-1:e.key==='ArrowDown'?(i+1)%visible.length:(i<=0?visible.length-1:i-1);visible[next].focus();}});
 picker.addEventListener('focusout',()=>setTimeout(()=>{if(!picker.contains(document.activeElement))close();},0));
 panel.append(search,list,empty);picker.append(trigger,panel);field.append(picker);select.hidden=true;sync();pickers.push({picker,close});
}
document.addEventListener('pointerdown',e=>pickers.forEach(p=>{if(!p.picker.contains(e.target))p.close();}));
})();
