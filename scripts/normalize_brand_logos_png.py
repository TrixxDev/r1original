#!/usr/bin/env python3
"""Replace .svg brand logos with .png where possible (1000logos or SVG conversion)."""

from __future__ import annotations

import json
import os
import sys

sys.path.insert(0, os.path.dirname(__file__))

from download_brand_logos_1000logos import (  # noqa: E402
    OUT_DIR,
    ext_from_url,
    extract_logo_url,
    fetch,
    fetch_text,
    log,
    page_candidates,
)

MANIFEST_PATH = os.path.join(OUT_DIR, 'manifest.json')


def load_manifest() -> dict[str, str]:
    if not os.path.isfile(MANIFEST_PATH):
        return {}
    with open(MANIFEST_PATH, encoding='utf-8') as f:
        data = json.load(f)
    return data if isinstance(data, dict) else {}


def save_manifest(manifest: dict[str, str]) -> None:
    with open(MANIFEST_PATH, 'w', encoding='utf-8') as f:
        json.dump(manifest, f, indent=2, ensure_ascii=False)


def download_png_from_1000logos(slug: str) -> str | None:
    for page_slug in page_candidates(slug):
        html = fetch_text(f'https://1000logos.net/{page_slug}-logo/')
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
        return filename
    return None


def convert_svg_to_png(slug: str) -> str | None:
    svg_path = os.path.join(OUT_DIR, f'{slug}.svg')
    if not os.path.isfile(svg_path):
        return None
    try:
        import cairosvg
    except (ImportError, OSError):
        return None

    png_path = os.path.join(OUT_DIR, f'{slug}.png')
    try:
        cairosvg.svg2png(url=svg_path, write_to=png_path, output_width=400)
    except Exception:
        return None

    if os.path.isfile(png_path) and os.path.getsize(png_path) > 400:
        return f'{slug}.png'
    return None


def remove_file(path: str) -> None:
    if os.path.isfile(path):
        os.remove(path)


def main() -> int:
    manifest = load_manifest()
    svg_slugs = [
        f[:-4] for f in os.listdir(OUT_DIR)
        if f.endswith('.svg') and f != 'manifest.json'
    ]

    if not svg_slugs:
        log('No .svg logos to normalize.')
        return 0

    log(f'Found {len(svg_slugs)} .svg logos to normalize.')

    for slug in sorted(svg_slugs):
        new_file = download_png_from_1000logos(slug)
        source = '1000logos'

        if not new_file:
            new_file = convert_svg_to_png(slug)
            source = 'converted'

        if not new_file:
            log(f'- {slug}: kept .svg (no PNG source)')
            continue

        remove_file(os.path.join(OUT_DIR, f'{slug}.svg'))
        manifest[slug] = new_file
        log(f'+ {slug}: {new_file} ({source})')

    save_manifest(manifest)
    log('Manifest updated.')
    return 0


if __name__ == '__main__':
    sys.exit(main())
