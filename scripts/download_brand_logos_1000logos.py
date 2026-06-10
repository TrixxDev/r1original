#!/usr/bin/env python3
"""Fast download of tire brand logos from 1000logos.net (no Laravel bootstrap)."""

from __future__ import annotations

import json
import os
import re
import sys
import time
import urllib.error
import urllib.request

OUT_DIR = os.path.join(os.path.dirname(__file__), '..', 'public', 'images', 'brand-logos')
BRANDS_FILE = os.path.join(os.path.dirname(__file__), 'brands_list.json')
TIMEOUT = 12
USER_AGENT = 'Mozilla/5.0 (compatible; R1RiepasLogoBot/1.0)'

# Our slug -> 1000logos page slug (without "-logo")
PAGE_SLUGS: dict[str, str] = {
    'bf-goodrich': 'bfgoodrich',
    'nexen': 'nexen-tire',
    'toyo': 'toyo-tires',
    'diamond-back': 'diamondback',
    'gt-radial': 'gt-radial',
    'green-max': 'green-max',
    'road-rider': 'road-rider',
    'venom-power': 'venom-power',
    'tri-ace': 'tri-ace',
    'insa-turbo': 'insa-turbo',
    'collins': 'collins-tires',
    'cheng-shin': 'maxxis',
    'marshal': 'marshal-tires',
    'nordman': 'nordman',
    'laufenn': 'laufenn',
    'firestone': 'firestone',
    'cooper': 'cooper-tires',
    'vredestein': 'vredestein',
    'kleber': 'kleber',
    'tigar': 'tigar',
    'debica': 'debica',
    'barum': 'barum',
    'sava': 'sava',
    'giti': 'giti',
    'sailun': 'sailun',
    'triangle': 'triangle',
    'linglong': 'linglong',
    'westlake': 'westlake',
    'nokian': 'nokian',
    'rotalla': 'rotalla',
    'goodride': 'goodride',
    'kenda': 'kenda',
    'taurus': 'taurus',
    'doublestar': 'doublestar',
    'austone': 'austone',
    'sunny': 'sunny',
    'roadstone': 'roadstone',
    'riken': 'riken',
    'dayton': 'dayton',
    'kelly': 'kelly-tires',
    'profil': 'profil',
    'uniroyal': 'uniroyal',
    'yokohama': 'yokohama',
    'goodyear': 'goodyear',
    'dunlop': 'dunlop',
    'kumho': 'kumho',
    'maxxis': 'maxxis',
    'lassa': 'lassa',
    'falken': 'falken',
    'hankook': 'hankook',
    'michelin': 'michelin',
    'continental': 'continental',
    'pirelli': 'pirelli',
    'bridgestone': 'bridgestone',
}


def log(msg: str) -> None:
    try:
        print(msg, flush=True)
    except UnicodeEncodeError:
        print(msg.encode('ascii', errors='replace').decode('ascii'), flush=True)


def fetch(url: str) -> bytes | None:
    req = urllib.request.Request(url, headers={'User-Agent': USER_AGENT})
    try:
        with urllib.request.urlopen(req, timeout=TIMEOUT) as resp:
            return resp.read()
    except Exception:
        return None


def fetch_text(url: str) -> str | None:
    data = fetch(url)
    if not data:
        return None
    return data.decode('utf-8', errors='replace')


def extract_logo_url(html: str) -> str | None:
    block = html
    m = re.search(r'class="entry-content"(.*?)class="entry-footer"', html, re.S)
    if m:
        block = m.group(1)

    candidates: list[str] = []
    for match in re.finditer(
        r'(?:data-src|src)="(https://1000logos\.net/wp-content/uploads/[^"]+\.(?:png|jpe?g|svg))"',
        block,
        re.I,
    ):
        url = match.group(1)
        low = url.lower()
        if any(x in low for x in ('tumb', 'history', 'symbol', 'font-', 'color-', 'emblem', 'thumb', 'icon-')):
            continue
        candidates.append(url)

    if not candidates:
        return None

    def score(url: str) -> tuple[int, int]:
        low = url.lower()
        s = 10 if '-logo' in low else 0
        s += 3 if 'tire' in low else 0
        s += 2 if low.endswith('.png') else 0
        size = 0
        sm = re.search(r'-(\d+)x(\d+)\.', low)
        if sm:
            size = int(sm.group(1)) * int(sm.group(2))
        return (s, size)

    candidates.sort(key=score, reverse=True)
    best = candidates[0]
    full = re.sub(r'-\d+x\d+\.(png|jpe?g|svg)$', r'.\1', best, flags=re.I)
    return full if full != best else best


def page_candidates(slug: str) -> list[str]:
    out: list[str] = []
    if slug in PAGE_SLUGS:
        out.append(PAGE_SLUGS[slug])
    if slug not in out:
        out.append(slug)
    compact = slug.replace('-', '')
    if compact not in out:
        out.append(compact)
    return out[:3]


def ext_from_url(url: str) -> str:
    path = url.split('?', 1)[0].lower()
    for ext in ('.png', '.jpg', '.jpeg', '.svg', '.webp'):
        if path.endswith(ext):
            return ext
    return '.png'


def has_logo(slug: str) -> bool:
    for ext in ('.png', '.jpg', '.jpeg', '.svg', '.webp'):
        if os.path.isfile(os.path.join(OUT_DIR, slug + ext)):
            return True
    return False


def main() -> int:
    os.makedirs(OUT_DIR, exist_ok=True)

    with open(BRANDS_FILE, encoding='utf-8') as f:
        brands: list[list[str]] = json.load(f)

    manifest_path = os.path.join(OUT_DIR, 'manifest.json')
    manifest: dict[str, str] = {}
    if os.path.isfile(manifest_path):
        with open(manifest_path, encoding='utf-8') as f:
            data = json.load(f)
            if isinstance(data, dict):
                manifest = data

    ok = skip = fail = 0
    total = len(brands)

    for i, (slug, title) in enumerate(brands, 1):
        if has_logo(slug):
            log(f'[{i}/{total}] = {slug} skip (exists)')
            skip += 1
            continue

        saved = False
        for page_slug in page_candidates(slug):
            page = f'https://1000logos.net/{page_slug}-logo/'
            html = fetch_text(page)
            if not html or 'entry-content' not in html:
                continue

            logo_url = extract_logo_url(html)
            if not logo_url:
                continue

            data = fetch(logo_url)
            if not data or len(data) < 400:
                continue

            ext = ext_from_url(logo_url)
            filename = f'{slug}{ext}'
            with open(os.path.join(OUT_DIR, filename), 'wb') as fh:
                fh.write(data)
            manifest[slug] = filename
            log(f'[{i}/{total}] + {slug} <- {page_slug}')
            ok += 1
            saved = True
            break

        if not saved:
            log(f'[{i}/{total}] - {slug} ({title}) not on 1000logos')
            fail += 1

        time.sleep(0.25)

    with open(manifest_path, 'w', encoding='utf-8') as f:
        json.dump(manifest, f, indent=2, ensure_ascii=False)

    log(f'\nDone: +{ok} new, ={skip} skipped, -{fail} missing, manifest total: {len(manifest)}')
    return 0


if __name__ == '__main__':
    sys.exit(main())
