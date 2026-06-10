#!/usr/bin/env python3
import json
import os

OUT_DIR = os.path.join(os.path.dirname(__file__), '..', 'public', 'images', 'brand-logos')
manifest = {}
for name in sorted(os.listdir(OUT_DIR)):
    if name == 'manifest.json':
        continue
    slug, ext = os.path.splitext(name)
    if ext.lower() in {'.png', '.jpg', '.jpeg', '.svg', '.webp'}:
        manifest[slug] = name

with open(os.path.join(OUT_DIR, 'manifest.json'), 'w', encoding='utf-8') as f:
    json.dump(manifest, f, indent=2, ensure_ascii=False)

print(f'manifest: {len(manifest)} entries')
