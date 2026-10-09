import fs from 'node:fs/promises';
import path from 'node:path';
import assert from 'node:assert/strict';
const root=path.resolve(import.meta.dirname,'..');
const required=['functions.php','style.css','index.php','header.php','footer.php','front-page.php','inc/page-templates.php','pages/page.php','woocommerce/archive-product.php','woocommerce/content-single-product.php','assets/site.css'];
for(const slug of ['about','age-policy','bulk-orders','cart','checkout','contact','my-account','privacy-policy','returns','shipping','terms-and-conditions'])required.push(`pages/page-${slug}.php`);
for(const file of required)await fs.access(path.join(root,file));
const entries=await fs.readdir(root);assert(!entries.some(name=>name.endsWith('.html')),'Retired static HTML found in theme root');
const css=await fs.readFile(path.join(root,'assets/drop.css'),'utf8');
for(const [name,color] of Object.entries({bg:'#FAF7F2',primary:'#6C2BFF',accent:'#C6FF3D',pop:'#FF5C39',soft:'#E9E0FF',ink:'#14121F'}))assert(css.includes(`--${name}:${color}`),`Missing design token ${name}`);
for(let i=0;i<12;i++){await fs.access(path.join(root,`assets/images/shisha-product-${i}.webp`));await fs.access(path.join(root,`assets/images/product-cutout-${i}.webp`));}
console.log('WordPress structure and asset checks passed: page templates, core files and six design tokens.');
