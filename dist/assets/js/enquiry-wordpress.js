/* CF7 owns validation and delivery; the theme only enhances the live page. */
(()=>{'use strict';
const topic=document.querySelector('#contact-topic'),group=document.querySelector('#order-ref-group');
function update(){if(group)group.hidden=topic?.value!=='Order support';}
topic?.addEventListener('change',update);update();
if(window.gsap&&!matchMedia('(prefers-reduced-motion: reduce)').matches){gsap.from('.contact-hero-content',{y:18,opacity:0,duration:.65,ease:'power2.out'});}
})();
