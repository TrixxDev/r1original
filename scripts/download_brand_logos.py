#!/usr/bin/env python3
"""Download tire brand logos from Wikimedia Commons into public/images/brand-logos/."""

import json
import os
import time
import urllib.parse
import urllib.request

OUT_DIR = os.path.join(os.path.dirname(__file__), '..', 'public', 'images', 'brand-logos')
DELAY_SEC = 2.5
USER_AGENT = 'R1RiepasLogoBot/1.0 (local dev; contact: admin@r1riepas.lv)'

# slug -> list of Wikimedia Commons filenames (first match wins)
BRAND_FILES = {
    'michelin': ['Michelin_Wordmark.svg', 'Michelin logo.svg'],
    'continental': ['Continental_logo.svg', 'Continental wordmark.svg'],
    'goodyear': ['Goodyear_Tire_and_Rubber_Company_logo.svg', 'Goodyear wordmark.svg'],
    'bridgestone': ['Bridgestone_logo.svg'],
    'pirelli': ['Pirelli_logo.svg'],
    'hankook': ['Logo Hankook Tire 2025.svg', 'Hankook Tire logo.svg', 'Hankook Logo 2024.svg'],
    'yokohama': ['Yokohama Rubber Company logo.svg', 'Yokohama_Rubber_Company_logo.svg'],
    'nokian': ['Nokian Tyres logo.svg', 'Nokian_Tyres_logo.svg'],
    'kumho': ['Kumho Tire logo.svg', 'Kumho_Tire_logo.svg'],
    'dunlop': ['Dunlop logo.svg', 'Dunlop_logo.svg'],
    'falken': ['Falken Tire logo.svg', 'Falken_Tire_logo.svg'],
    'toyo': ['Toyo Tires logo.svg', 'Toyo_Tires_logo.svg'],
    'maxxis': ['Maxxis logo.svg', 'Maxxis_logo.svg'],
    'nankang': ['Nankang Rubber Tire logo.svg'],
    'bf-goodrich': ['BFGoodrich logo.svg', 'BFGoodrich_logo.svg'],
    'firestone': ['Firestone Tire and Rubber Company logo.svg', 'Firestone_logo.svg'],
    'cooper': ['Cooper Tire logo.svg', 'Cooper_Tire_logo.svg'],
    'uniroyal': ['Uniroyal logo.svg', 'Uniroyal_logo.svg'],
    'vredestein': ['Vredestein logo.svg', 'Vredestein_logo.svg'],
    'kleber': ['Kleber logo.svg'],
    'kormoran': ['Kormoran logo.svg'],
    'tigar': ['Tigar logo.svg'],
    'debica': ['Debica logo.svg'],
    'barum': ['Barum logo.svg'],
    'fulda': ['Fulda logo.svg'],
    'sava': ['Sava logo.svg'],
    'gislaved': ['Gislaved logo.svg'],
    'general-tire': ['General Tire logo.svg', 'General_Tire_logo.svg'],
    'nexen': ['Nexen Tire logo.svg', 'Nexen_Tire_logo.svg'],
    'lassa': ['Lassa logo.svg'],
    'marshal': ['Marshal logo.svg'],
    'giti': ['Giti Tire logo.svg', 'Giti_Tire_logo.svg'],
    'sailun': ['Sailun logo.svg', 'Sailun_Group_logo.svg'],
    'triangle': ['Triangle Group logo.svg', 'Triangle_Group_logo.svg'],
    'linglong': ['Linglong logo.svg', 'Linglong_logo.svg'],
    'westlake': ['Westlake logo.svg', 'Westlake_logo.svg'],
    'austone': ['Austone logo.svg'],
    'sunny': ['Sunny tire logo.svg'],
    'roadstone': ['Roadstone logo.svg'],
    'riken': ['Riken logo.svg'],
    'nordman': ['Nordman logo.svg'],
    'laufenn': ['Laufenn logo.svg'],
    'kelly': ['Kelly Tires logo.svg'],
    'dayton': ['Dayton logo.svg'],
    'insa-turbo': ['Insa Turbo logo.svg'],
    'doublestar': ['Doublestar logo.svg'],
    'rotalla': ['Rotalla logo.svg'],
    'rovelo': ['Rovelo logo.svg'],
    'tracmax': ['Tracmax logo.svg'],
    'evergreen': ['Evergreen Tire logo.svg'],
    'hifly': ['Hifly logo.svg'],
    'atturo': ['Atturo logo.svg'],
    'arivo': ['Arivo logo.svg'],
    'imperial': ['Imperial Tires logo.svg'],
    'kenda': ['Kenda logo.svg', 'Kenda Rubber logo.svg'],
    'taurus': ['Taurus logo.svg'],
    'tourador': ['Tourador logo.svg'],
    'sonix': ['Sonix logo.svg'],
    'sunfull': ['Sunfull logo.svg'],
    'sunwide': ['Sunwide logo.svg'],
    'goodride': ['Goodride logo.svg'],
    'gripmax': ['Gripmax logo.svg'],
    'gt-radial': ['GT Radial logo.svg'],
    'green-max': ['Green Max logo.svg'],
    'ecovision': ['Ecovision logo.svg'],
    'dynamo': ['Dynamo logo.svg'],
    'firemax': ['Firemax logo.svg'],
    'federal': ['Federal Corporation logo.svg', 'Federal logo.svg'],
    'fortune': ['Fortune logo.svg'],
    'powertrac': ['Powertrac logo.svg'],
    'roadcruza': ['Roadcruza logo.svg'],
    'road-rider': ['Road Rider logo.svg'],
    'roadmarch': ['Roadmarch logo.svg'],
    'roadx': ['RoadX logo.svg'],
    'rockblade': ['Rockblade logo.svg'],
    'trazano': ['Trazano logo.svg'],
    'tri-ace': ['Tri-Ace logo.svg'],
    'venom-power': ['Venom Power logo.svg'],
    'winrun': ['Winrun logo.svg'],
    'zelda': ['Zelda logo.svg'],
    'zmax': ['Zmax logo.svg'],
    'mirage': ['Mirage tire logo.svg'],
    'nereus': ['Nereus logo.svg'],
    'norrsken': ['Norrsken logo.svg'],
    'ovation': ['Ovation tire logo.svg'],
    'cheng-shin': ['Cheng Shin logo.svg', 'Maxxis logo.svg'],
    'diamond-back': ['Diamondback logo.svg'],
    'antares': ['Antares tire logo.svg'],
    'aptany': ['Aptany logo.svg'],
    'altenzo': ['Altenzo logo.svg'],
    'blacklion': ['Blacklion logo.svg'],
    'cachland': ['Cachland logo.svg'],
    'comforser': ['Comforser logo.svg'],
    'farroad': ['Farroad logo.svg'],
    'grenlander': ['Grenlander logo.svg'],
    'haida': ['Haida tire logo.svg'],
    'ilink': ['iLink logo.svg'],
    'journey': ['Journey tire logo.svg'],
    'kustone': ['Kustone logo.svg'],
    'malatesta': ['Malatesta logo.svg'],
    'maxtrek': ['Maxtrek logo.svg'],
    'milever': ['Milever logo.svg'],
    'profil': ['Profil logo.svg'],
    'silverstone': ['Silverstone tire logo.svg'],
    'superia': ['Superia logo.svg'],
    'vitour': ['Vitour logo.svg'],
    'volcato': ['Volcato logo.svg'],
}


def download_commons(filename: str) -> bytes | None:
    url = 'https://commons.wikimedia.org/wiki/Special:FilePath/' + urllib.parse.quote(filename)
    req = urllib.request.Request(url, headers={'User-Agent': USER_AGENT})
    try:
        with urllib.request.urlopen(req, timeout=60) as resp:
            data = resp.read()
            ctype = resp.headers.get('content-type', '')
            if 'image' not in ctype and 'svg' not in ctype:
                return None
            if len(data) < 200:
                return None
            return data
    except Exception as exc:
        print(f'  skip {filename}: {exc}')
        return None


def ext_for(data: bytes, filename: str) -> str:
    if filename.lower().endswith('.png'):
        return '.png'
    if filename.lower().endswith('.jpg') or filename.lower().endswith('.jpeg'):
        return '.jpg'
    if data[:4] == b'\x89PNG':
        return '.png'
    if data[:2] == b'\xff\xd8':
        return '.jpg'
    return '.svg'


def main():
    os.makedirs(OUT_DIR, exist_ok=True)
    manifest = {}
    ok = 0
    fail = 0

    for slug, candidates in BRAND_FILES.items():
        dest_base = os.path.join(OUT_DIR, slug)
        for existing in (dest_base + '.svg', dest_base + '.png', dest_base + '.jpg'):
            if os.path.isfile(existing):
                manifest[slug] = os.path.basename(existing)
                print(f'= {slug} already exists')
                ok += 1
                break
        else:
            saved = False
            for filename in candidates:
                print(f'-> {slug}: trying {filename}')
                data = download_commons(filename)
                time.sleep(DELAY_SEC)
                if data:
                    ext = ext_for(data, filename)
                    out = dest_base + ext
                    with open(out, 'wb') as f:
                        f.write(data)
                    manifest[slug] = os.path.basename(out)
                    print(f'  OK {out} ({len(data)} bytes)')
                    ok += 1
                    saved = True
                    break
            if not saved:
                print(f'  FAIL {slug}')
                fail += 1

    manifest_path = os.path.join(OUT_DIR, 'manifest.json')
    with open(manifest_path, 'w', encoding='utf-8') as f:
        json.dump(manifest, f, indent=2, ensure_ascii=False)

    print(f'\nDone: {ok} logos, {fail} missing. Manifest: {manifest_path}')


if __name__ == '__main__':
    main()
