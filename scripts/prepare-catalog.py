"""Normalize the verified public export for a draft-only WooCommerce import."""
import argparse
import json
import re
from pathlib import Path

from bs4 import BeautifulSoup


def editorial(record):
    sections = record['page']['pre_faq_sections']
    description = next(s for s in sections if s['heading'] == 'Product Description')
    soup = BeautifulSoup(description['html'], 'html.parser')
    for heading in soup.select('h2.detail-section-title'):
        heading.decompose()
    specs, included = [], []
    in_items = False
    promotional = False
    for node in list(soup.select('p')):
        text = node.get_text(' ', strip=True)
        if re.search(r'free\s+item|free\s+gift', text, re.I):
            promotional = True
        if promotional:
            node.decompose()
            continue
        if re.fullmatch(r'(specifications?|product specifications?)', text, re.I):
            node.decompose()
            continue
        if re.fullmatch(r'(included items|in the box|package includes)\s*[:!]*', text, re.I):
            in_items = True
            node.decompose()
            continue
        if in_items:
            if text:
                included.append({'item': text})
            node.decompose()
            continue
        match = re.match(r'^([^:\n]{1,55}?)\s*(?:\s[-–]\s|:)\s*(.+)$', text)
        if match:
            label = match[1].strip().lstrip('- ').strip()
            if label.lower() in ('package included', 'the package includes also', 'items', 'components'):
                included.append({'item': match[2].strip()})
            elif len(label) > 40 or label.lower() in ('note', 'description') or re.search(r'highlights|technical details', label, re.I):
                continue
            else:
                specs.append({'label': label, 'value': match[2].strip()})
            node.decompose()
    # Keep prose in WooCommerce; parsed specifications/items have one editorial owner.
    overview = ''.join(str(n) for n in (soup.select_one('.description-content') or soup).contents).strip()
    if not BeautifulSoup(overview, 'html.parser').get_text(strip=True):
        overview = ''
    features, usage = [], []
    for section in sections:
        for table in section.get('tables', []):
            if section['heading'] == 'Hookah Features':
                for row in table['rows'][1:]:
                    if len(row) >= 3:
                        available = row[1].strip().lower()
                        features.append({'feature': row[0], 'availability': available if available in ('yes', 'no') else 'unspecified', 'notes': row[2]})
            elif section['heading'] == 'Usage Instructions':
                usage.extend({'step': r[0], 'instructions': r[1]} for r in table['rows'][1:] if len(r) >= 2)
    return {'description': overview, 'specifications': specs, 'features': features, 'included_items': included, 'usage_steps': usage}


def classification(record):
    c = record['catalogue_product']
    cols = set(record['collections'])
    cats = []
    names = {'heat-management': 'Heat Management', 'chillum': 'Bowls', 'bowls': 'Bowls', 'coal-burners': 'Coal Burners', 'hookah-bags': 'Hookah Bags', 'tongs': 'Tongs', 'charcoal': 'Charcoal', 'portable-hookahs': 'Portable Hookahs'}
    if c['product_type'].lower() == 'hookah' or 'hookah' in c['title'].lower() and c['product_type'].lower() != 'accessories':
        cats.append('Hookahs')
    if c['product_type'].lower() == 'accessories' or 'accessories' in cols:
        cats.append('Accessories')
    cats.extend(names[x] for x in sorted(cols) if x in names)
    if not cats:
        raise ValueError('Unmapped category: ' + c['title'])
    vendor = c['vendor'].strip()
    # Source store vendor is not a manufacturer brand. Retain it in provenance only.
    brand = {'cocoyaya': 'COCOYAYA', 'vg-france': 'VG-France', 'alshan': 'Alshan'}.get(vendor.lower())
    return list(dict.fromkeys(cats)), brand


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--data', type=Path, required=True)
    parser.add_argument('--output', type=Path, required=True)
    args = parser.parse_args()
    records = json.loads((args.data / 'products.json').read_text())
    products = []
    media = json.loads((args.data / 'media-manifest.json').read_text())
    for r in records:
        c = r['catalogue_product']
        cats, brand = classification(r)
        variants = r['variants_comprehensive']
        options = []
        for option in c['options']:
            if option['name'] == 'Title' and option['values'] == ['Default Title']:
                continue
            # All non-default options in this source catalogue are colour selections.
            label = 'Colour' if option['name'].lower() in ('color', 'colour', 'blue red') else option['name']
            options.append({'name': label, 'source_position': option['position'], 'values': option['values']})
        image_order = [i['src'] for i in c['images']]
        products.append({
            'source_id': str(r['source_product_id']), 'source_url': r['source_url'],
            'handle': c['handle'], 'title': c['title'], 'vendor': c['vendor'].strip(),
            'source_collections': r['collections'], 'source_tags': c.get('tags', []),
            'categories': cats, 'brand': brand, 'options': options,
            'type': 'variable' if options else 'simple',
            'variants': [{
                'source_id': str(v['id']), 'title': v['title'],
                'options': [v.get('option' + str(o['source_position'])) for o in options],
                'price': v['price'], 'compare_at_price': v.get('compare_at_price'),
                'source_sku': v.get('sku'), 'barcode': v.get('barcode'),
                'available_at_source': v.get('available'),
                'requires_shipping': v.get('requires_shipping', True),
                'weight': v.get('weight', 0), 'weight_unit': v.get('weight_unit', 'kg'),
                'image_url': (v.get('featured_image') or {}).get('src'),
                'currency': v.get('price_currency', 'INR'),
            } for v in variants],
            'image_urls': image_order, 'editorial': editorial(r),
        })
    assert len({p['source_id'] for p in products}) == len(products) == 107
    assert sum(len(p['variants']) for p in products) == 142
    assert len(media) == 304
    for p in products:
        assert len(p['variants']) == 1 or p['options'], p['title']
        for v in p['variants']:
            assert v['currency'] == 'INR'
            assert all(v['options'][i] in o['values'] for i, o in enumerate(p['options']))
    plan = {'schema': 1, 'source': 'https://shishastore.in', 'source_export_date': '2026-10-09', 'currency': 'INR', 'media': media, 'products': products}
    args.output.write_text(json.dumps(plan, ensure_ascii=False, indent=2) + '\n')
    print(json.dumps({'products': len(products), 'simple': sum(p['type'] == 'simple' for p in products), 'variable': sum(p['type'] == 'variable' for p in products), 'variants': sum(len(p['variants']) for p in products), 'images': len(media), 'specification_rows': sum(len(p['editorial']['specifications']) for p in products), 'feature_rows': sum(len(p['editorial']['features']) for p in products), 'usage_rows': sum(len(p['editorial']['usage_steps']) for p in products)}, indent=2))


if __name__ == '__main__':
    main()
