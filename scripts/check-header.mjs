/** Browser checks against real WordPress and its rendered guest snapshot. */
import fs from 'node:fs/promises';
import assert from 'node:assert/strict';
const {chromium}=await import(process.env.URBAN_PLAYWRIGHT_MODULE||'playwright');
const directory=process.argv[2]||'/tmp/urban-header-review';
const origin=process.env.URBAN_WP_URL||'http://urban-shisha.local';
const auth=JSON.parse(await fs.readFile(`${directory}/auth.json`,'utf8'));
const browser=await chromium.launch({headless:true,executablePath:process.env.URBAN_CHROME||'/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'});
const results=[];
try{
 const context=await browser.newContext();
 await context.addCookies(auth.cookies);
 await context.addInitScript(()=>localStorage.setItem('urban-preview-age','accepted'));
 const page=await context.newPage();page.setDefaultTimeout(10000);
 const errors=[];page.on('pageerror',e=>errors.push(e.message));
 for(const width of [1440,1024,768,390,320]){
  await page.setViewportSize({width,height:1000});
  await page.goto(origin,{waitUntil:'networkidle'});
  assert(await page.locator('.site-header').isVisible());
  assert.equal(await page.locator('.mega-category').count(),6);
  assert.equal(await page.locator('.mobile-category').count(),6);
  assert(await page.locator('.account-nav').isVisible());
  assert.equal(await page.locator('.account-menu .account-signin').textContent().then(s=>s.trim()),'Account dashboard');
  const overflow=await page.evaluate(()=>document.documentElement.scrollWidth>innerWidth+1);
  assert(!overflow,`Overflow at ${width}`);
  if(await page.locator('.mobile-toggle').isVisible()){
   await page.locator('.mobile-toggle').click();
   assert(await page.locator('#mobile-nav').isVisible(),`Mobile panel hidden at ${width}: ${await page.locator('#mobile-nav').evaluate(el=>JSON.stringify({hidden:el.hidden,display:getComputedStyle(el).display,rect:el.getBoundingClientRect().toJSON()}))}`);
   assert.equal(await page.locator('.mobile-toggle').getAttribute('aria-expanded'),'true');
   await page.screenshot({path:`${directory}/mobile-${width}.png`});
   await page.keyboard.press('Escape');assert(!(await page.locator('#mobile-nav').isVisible()));
  }else{
   await page.locator('#mega-toggle').hover();assert(await page.locator('#category-menu').isVisible());
   const complete=await page.locator('.mega-photo img').evaluateAll(imgs=>imgs.every(i=>i.complete&&i.naturalWidth>0));
   assert(complete,`Mega menu images incomplete at ${width}`);
   await page.screenshot({path:`${directory}/desktop-${width}.png`});
   await page.locator('#mega-toggle').focus();await page.keyboard.press('ArrowDown');
   assert(await page.locator('#category-menu a').first().evaluate(el=>el===document.activeElement));
   await page.keyboard.press('Escape');assert(!(await page.locator('#category-menu').isVisible()));
  }
  await page.locator('#account-trigger').click();assert(await page.locator('#account-shortcuts').isVisible());
  const rect=await page.locator('#account-shortcuts').boundingBox();assert(rect.x>=0&&rect.x+rect.width<=width+1);
  await page.keyboard.press('Escape');assert(!(await page.locator('#account-shortcuts').isVisible()));
  await page.locator('[data-open="search"]').click();assert(await page.locator('#header-search').isVisible());
  assert(await page.locator('#header-search-input').evaluate(el=>el===document.activeElement));
  await page.keyboard.press('Escape');assert(!(await page.locator('#header-search').isVisible()));
  results.push({width,passed:true});
 }
 // Verify the real cart AJAX response includes the native badge fragment.
 const response=await context.request.post(`${origin}/?wc-ajax=get_refreshed_fragments`);
 assert(response.ok());const payload=await response.json();assert(payload.fragments['.site-header .cart-count']);
 // Search submits native WordPress product parameters.
 await page.locator('[data-open="search"]').click();await page.locator('#header-search-input').fill('Alexander');
 await Promise.all([page.waitForURL(u=>u.searchParams.get('s')==='Alexander'),page.locator('.header-product-search button[type="submit"]').click()]);
 assert.equal(new URL(page.url()).searchParams.get('post_type'),'product');
 // Shared header remains present on all existing WooCommerce system pages.
 for(const path of ['/shop/','/cart/','/checkout/','/my-account/']){
  await page.goto(origin+path,{waitUntil:'networkidle'});assert(await page.locator('.account-nav').isVisible());
 }
 const guest=await browser.newContext({viewport:{width:1440,height:1000}});
 await guest.addInitScript(()=>localStorage.setItem('urban-preview-age','accepted'));
 const guestHtml=await fs.readFile(`${directory}/guest.html`,'utf8');
 const guestPage=await guest.newPage();guestPage.on('pageerror',e=>errors.push(e.message));
 // Establish the real local-site origin before loading the CLI-rendered snapshot.
 // Live logged-out visitors still see Coming Soon; this does not change store mode.
 await guestPage.goto(origin,{waitUntil:'networkidle'});
 // Woo's attribution custom element was already registered by the Coming Soon
 // document. Avoid registering that unrelated element twice in this same realm.
 const guestFixture=guestHtml.replace(/<script\b[^>]*\bid="wc-order-attribution-js"[^>]*>[\s\S]*?<\/script>/,'');
 await guestPage.setContent(guestFixture,{waitUntil:'networkidle'});
 assert((await guestPage.locator('.account-signin').textContent()).includes('Sign in / Register'));
 assert.equal(await guestPage.locator('.account-logout').count(),0);
 await guestPage.locator('#account-trigger').hover();assert(await guestPage.locator('#account-shortcuts').isVisible());
 assert.equal(await guestPage.locator('script[src*="catalog.js"],script[src*="config.js"]').count(),0);
 // A native three-level menu with no mega trigger must still work on mobile.
 const nestedHtml=(await fs.readFile(`${directory}/nested.html`,'utf8')).replace(/<script\b[^>]*\bid="wc-order-attribution-js"[^>]*>[\s\S]*?<\/script>/,'');
 await guestPage.goto(origin,{waitUntil:'networkidle'});await guestPage.setContent(nestedHtml,{waitUntil:'networkidle'});
 const parentToggle=guestPage.locator('.desktop-nav .nav-submenu-toggle').first();
 await parentToggle.focus();await guestPage.keyboard.press('ArrowDown');
 assert(await guestPage.locator('.desktop-nav .sub-menu').first().isVisible());
 await guestPage.locator('.desktop-nav .sub-menu .nav-submenu-toggle').hover();
 assert(await guestPage.locator('.desktop-nav a').filter({hasText:'External reference'}).isVisible());
 assert.equal(await guestPage.locator('.desktop-nav a').filter({hasText:'External reference'}).getAttribute('target'),'_blank');
 await guestPage.setViewportSize({width:390,height:1000});
 assert.equal(await guestPage.locator('#mega-toggle').count(),0);
 await guestPage.locator('.mobile-toggle').click();assert(await guestPage.locator('#mobile-nav').isVisible());
 await guestPage.locator('#mobile-nav .nav-submenu-toggle').first().click();
 assert(await guestPage.locator('#mobile-nav .mobile-sub-menu').first().isVisible());
 assert.deepEqual(errors,[]);
 const report={passed:true,viewports:results,guestState:true,authenticatedState:true,productSearch:true,cartAjaxFragment:true,systemPages:true,nativeNestedMenus:true,mobileWithoutMegaTrigger:true,errors};
 await fs.writeFile(`${directory}/browser-verification.json`,JSON.stringify(report,null,2)+'\n');
 console.log(JSON.stringify(report));
}finally{await browser.close();}
