#!/usr/bin/env python3
import re
import urllib.request

def fetch(url):
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(req, timeout=30) as r:
        return r.read().decode('utf-8', errors='replace')

html = fetch('https://1000logos.net/michelin-logo/')
for pat in ['entry-content', 'Michelin-logo', 'attachment-full', 'wp-block-image', 'logo-500']:
    i = html.find(pat)
    print(pat, 'at', i)
    if i >= 0:
        print(html[i:i+500])
        print('---')
