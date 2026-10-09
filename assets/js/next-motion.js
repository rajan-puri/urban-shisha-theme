/* Motion layer for the separate V2 preview. Layout and content stay visible without it. */
(() => {
'use strict';
const $=(s,r=document)=>r.querySelector(s),$$=(s,r=document)=>[...r.querySelectorAll(s)];
const reduced=()=>matchMedia('(prefers-reduced-motion:reduce)').matches;
let loaderSeen=false;
function start({ribbonTemplate}){
 const ctx=gsap.context(()=>{
  const cleanup=[];
  const listen=(el,event,fn,options)=>{if(!el)return;el.addEventListener(event,fn,options);cleanup.push(()=>el.removeEventListener(event,fn,options));};
  const introDelay=loaderSeen?0:.8;
  const loader=$('.page-loader');
  if(!loaderSeen){
   loaderSeen=true;
   if(loader){
    loader.hidden=false;
    const fallback=setTimeout(()=>loader.hidden=true,1400);
    cleanup.push(()=>{clearTimeout(fallback);loader.hidden=true;});
    gsap.timeline({onComplete:()=>{loader.hidden=true;clearTimeout(fallback);}})
     .from('.loader-wordmark',{y:14,opacity:0,duration:.25})
     .from('.loader-puff',{y:15,scale:.65,opacity:0,duration:.45},0)
     .to(loader,{yPercent:-105,duration:.4,ease:'power3.inOut'},.42);
   }
  }
  const hero=$('.hero');
  if(hero){
   const intro=gsap.timeline({delay:introDelay,defaults:{ease:'power3.out'}});
   if($('.headline-piece',hero))intro.from($$('.headline-piece',hero),{yPercent:110,duration:.85,stagger:.1,clearProps:'transform'},0);
   if($('.hero-eyebrow',hero))intro.from($('.hero-eyebrow',hero),{y:12,opacity:0,duration:.5,clearProps:'opacity,transform'},.08);
   if($('.hero-visual',hero))intro.from($('.hero-visual',hero),{y:-90,scale:.88,opacity:0,duration:1.15,ease:'back.out(1.3)',clearProps:'opacity,transform'},.12);
   if($('.hero-sticker .sticker-inner',hero))intro.from($$('.hero-sticker .sticker-inner',hero),{scale:.7,opacity:0,duration:.65,stagger:.07,ease:'back.out(1.5)',clearProps:'opacity,transform'},.45);
   if($('.hero-content',hero))intro.from($('.hero-content',hero),{y:20,opacity:0,duration:.65,clearProps:'opacity,transform'},.48);
   if($('.hero-smoke',hero))intro.from($('.hero-smoke',hero),{opacity:0,duration:.9,clearProps:'opacity'},.88);

   const floats=[];
   if($('.hero-float',hero))floats.push(gsap.to($('.hero-float',hero),{y:-13,duration:4,ease:'sine.inOut',repeat:-1,yoyo:true,delay:introDelay+1}));
   $$('.hero-sticker .sticker-inner',hero).forEach((el,i)=>{
    floats.push(gsap.to(el,{y:i%2?-8:7,duration:2.7+i*.4,ease:'sine.inOut',repeat:-1,yoyo:true,delay:introDelay+1}));
    listen(el,'pointerenter',()=>gsap.fromTo(el,{rotation:-3},{rotation:0,duration:.65,ease:'elastic.out(1,.5)',overwrite:'auto'}));
   });
   $$('.hero-smoke path',hero).forEach((path,i)=>floats.push(gsap.fromTo(path,{y:18,scale:.96,opacity:.25},{y:-32,scale:1.03,opacity:1,duration:5.5+i*.7,ease:'sine.inOut',repeat:-1,yoyo:true,delay:introDelay+.9,transformOrigin:'50% 100%'})));
   if(floats.length){
    const heroTrigger=ScrollTrigger.create({trigger:hero,start:'top bottom',end:'bottom top',onToggle:self=>floats.forEach(t=>self.isActive?t.resume():t.pause())});
    if(!heroTrigger.isActive)floats.forEach(t=>t.pause());
   }
  }

  // Transform-only card entrances: no text or entire section left semi-transparent.
  const reveal=el=>gsap.fromTo(el,{y:24},{y:0,duration:.55,ease:'power3.out',clearProps:'transform'});
  $$('.section-heading').forEach(heading=>{const title=$('h2',heading);if(title)gsap.fromTo(title,{'--marker-progress':0},{'--marker-progress':1,duration:.75,ease:'power3.out',scrollTrigger:{trigger:heading,start:'top 92%',once:true}});ScrollTrigger.create({trigger:heading,start:'top 95%',once:true,onEnter:()=>reveal(heading)});});
  ScrollTrigger.batch('.brand-tile,.category-card,.mood-card,.budget-card,.kit-card,.product-card,.spotlight-feature,.spotlight-choice',{start:'top 97%',once:true,onEnter:batch=>gsap.fromTo(batch,{y:22},{y:0,duration:.5,stagger:.04,ease:'power3.out',clearProps:'transform'})});

  // Local desktop interactions are removed cleanly when the viewport/motion setting changes.
  const media=gsap.matchMedia();
  media.add('(min-width:901px) and (hover:hover) and (pointer:fine)',()=>{
   const removers=[];const on=(el,type,fn)=>{if(!el)return;el.addEventListener(type,fn);removers.push(()=>el.removeEventListener(type,fn));};
   const heroEl=$('.hero'),product=$('.hero-parallax'),smoke=$('.hero-smoke');
   if(heroEl){
    const px=product?gsap.quickTo(product,'x',{duration:.65,ease:'power3.out'}):null,
          py=product?gsap.quickTo(product,'y',{duration:.65,ease:'power3.out'}):null,
          sx=smoke?gsap.quickTo(smoke,'x',{duration:1}):null,
          sy=smoke?gsap.quickTo(smoke,'y',{duration:1}):null;
    const stickers=$$('.hero-sticker',heroEl).map(el=>({x:gsap.quickTo(el,'x',{duration:.7}),y:gsap.quickTo(el,'y',{duration:.7})}));
    on(heroEl,'pointermove',e=>{const r=heroEl.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;if(px)px(-x*30);if(py)py(-y*22);if(sx)sx(-x*9);if(sy)sy(-y*7);stickers.forEach((s,i)=>{s.x(x*(34+i*3));s.y(y*(25+i*2));});});
    on(heroEl,'pointerleave',()=>{if(px)px(0);if(py)py(0);if(sx)sx(0);if(sy)sy(0);stickers.forEach(s=>{s.x(0);s.y(0);});});
   }
   $$('.category-card,.mood-card').forEach(card=>{
    on(card,'pointermove',e=>{const r=card.getBoundingClientRect();gsap.to(card,{rotationX:-(e.clientY-r.top-r.height/2)/r.height*4,rotationY:(e.clientX-r.left-r.width/2)/r.width*5,transformPerspective:900,duration:.35,overwrite:'auto'});});
    on(card,'pointerleave',()=>gsap.to(card,{rotationX:0,rotationY:0,duration:.4,clearProps:'transform'}));
    if(card.matches('.mood-card')){on(card,'pointerenter',()=>{const mp=$('.mood-product',card);if(mp)gsap.to(mp,{y:-16,rotation:2,duration:.55,ease:'power3.out'});const so=$('.scene-orbit',card);if(so)gsap.to(so,{rotation:6,scale:1.08,duration:.65});const dec=$('.scene-spark,.scene-hearts,.scene-wisp',card);if(dec)gsap.to(dec,{y:-9,rotation:5,duration:.7});});on(card,'pointerleave',()=>{const mp=$('.mood-product',card);if(mp)gsap.to(mp,{y:0,rotation:0,duration:.5,clearProps:'transform'});const so=$('.scene-orbit',card);if(so)gsap.to(so,{scale:1,rotation:0,duration:.55,clearProps:'transform'});const dec=$('.scene-spark,.scene-hearts,.scene-wisp',card);if(dec)gsap.to(dec,{y:0,rotation:0,duration:.5,clearProps:'transform'});});}
   });
   // Keep an ordinary cursor until the motion layer is active; drag cursor over rail only.
   const cursor=$('.studio-cursor');
   if(cursor){
    cursor.hidden=false;cursor.style.opacity='0';document.body.classList.add('has-studio-cursor');const cx=gsap.quickTo(cursor,'x',{duration:.13,ease:'power3.out'}),cy=gsap.quickTo(cursor,'y',{duration:.13,ease:'power3.out'});
    on(document,'pointermove',e=>{cx(e.clientX);cy(e.clientY);const rail=e.target.closest('.hookah-rail');const drag=!!rail&&!e.target.closest('input,select,textarea,.save-product,.add-product,.quick-add,.product-name');cursor.style.opacity=drag?'1':'0';cursor.classList.toggle('is-rail',drag);$$('.hookah-rail').forEach(el=>el.classList.toggle('is-drag-cursor',drag&&el===rail));});
    on(document,'pointerleave',()=>cursor.style.opacity='0');on(document,'pointerenter',()=>cursor.style.opacity='0');
    removers.push(()=>{cursor.hidden=true;document.body.classList.remove('has-studio-cursor');$$('.hookah-rail').forEach(el=>el.classList.remove('is-drag-cursor'));});
   }
   return()=>{removers.forEach(fn=>fn());gsap.killTweensOf('.hero-parallax,.hero-sticker,.hero-smoke,.category-card,.mood-card,.studio-cursor');};
  });

  // Seamless wrap uses two equal, viewport-covering halves and a modulo playhead.
  const ribbon=$('.ticker'),track=$('.ticker-track');
  if(ribbon && track){
   const copies=$$('.ticker-copy',track);
   if(copies.length > 0){
    let hovered=false,ribbonWidth=0,loopWidth=1,position=0,speed=1,targetSpeed=1,direction=1,targetDirection=1,visible=false;
    const tpl=ribbonTemplate||copies[0].innerHTML;
    const fillRibbon=()=>{if(ribbonWidth===ribbon.clientWidth)return;ribbonWidth=ribbon.clientWidth;copies[0].innerHTML=tpl;const base=copies[0].getBoundingClientRect().width||1;copies.forEach(copy=>copy.innerHTML=Array(Math.max(1,Math.ceil((ribbonWidth+1)/base))).fill(tpl).join(' '));loopWidth=copies[0].getBoundingClientRect().width||1;position=((position%loopWidth)+loopWidth)%loopWidth;};
    fillRibbon();
    const setX=gsap.quickSetter(track,'x','px');
    const tick=(_,delta)=>{if(!visible||hovered)return;const dt=Math.min(delta,50)/1000;speed+=(targetSpeed-speed)*Math.min(dt*4,1);direction+=(targetDirection-direction)*Math.min(dt*7,1);position=((position+dt*42*speed*direction)%loopWidth+loopWidth)%loopWidth;setX(-position);targetSpeed+=(1-targetSpeed)*Math.min(dt*2,1);};gsap.ticker.add(tick);
    const ribbonResize=new ResizeObserver(fillRibbon);ribbonResize.observe(ribbon);document.fonts.ready.then(()=>{ribbonWidth=0;fillRibbon();});
    ScrollTrigger.create({trigger:ribbon,start:'top bottom',end:'bottom top',onToggle:self=>visible=self.isActive});
    ScrollTrigger.create({onUpdate:self=>{const velocity=self.getVelocity();if(Math.abs(velocity)>30){targetDirection=velocity<0?-1:1;targetSpeed=1+Math.min(Math.abs(velocity)/1800,1.4);}}});
    listen(ribbon,'pointerenter',()=>hovered=true);listen(ribbon,'pointerleave',()=>hovered=false);
    cleanup.push(()=>{ribbonResize.disconnect();gsap.ticker.remove(tick);gsap.set(track,{clearProps:'transform'});});
   }
  }

  $$('.footer-letter').forEach((el,i)=>el.style.setProperty('--letter-index',i));
  if($('.footer-wordmark'))gsap.from('.footer-letter',{yPercent:80,duration:.8,stagger:.045,ease:'power3.out',clearProps:'transform',scrollTrigger:{trigger:'.footer-wordmark',start:'top 94%',once:true}});

  // The controls also work with keyboard, touch and reduced motion (wired in home.js).
  document.fonts.ready.then(()=>ScrollTrigger.refresh());
  return()=>{cleanup.forEach(fn=>fn());media.revert();};
 });
 return ctx;
}
function added(button){if(reduced()||!window.gsap)return;const bag=$('.bag-button>svg'),badge=$('.bag-button .cart-count');gsap.fromTo(bag,{y:0,rotation:0},{y:-6,rotation:-10,duration:.16,yoyo:true,repeat:1,ease:'power2.out',clearProps:'transform'});gsap.fromTo(badge,{scale:.8},{scale:1,duration:.4,ease:'back.out(2)',clearProps:'transform'});if(button)gsap.fromTo(button,{scale:.85},{scale:1,duration:.35,ease:'back.out(2)',clearProps:'transform'});}
window.UrbanMotion={start,added};
})();
