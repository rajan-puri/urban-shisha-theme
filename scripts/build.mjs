import fs from 'node:fs/promises';import path from 'node:path';
const sourceRoot=path.resolve(import.meta.dirname,'..');
const styles=['fonts.css','style.css','refinements.css','drop.css','next-edit.css','shop.css','product.css','cart.css','checkout.css','account.css','contact.css','wholesale.css','pages.css','navigation.css'];
await fs.writeFile(path.join(sourceRoot,'assets/site.css'),(await Promise.all(styles.map(name=>fs.readFile(path.join(sourceRoot,'assets',name),'utf8')))).join('\n'));
await import('./verify.mjs');
console.log('Build complete: WordPress assets/site.css — no static HTML or dist output.');
