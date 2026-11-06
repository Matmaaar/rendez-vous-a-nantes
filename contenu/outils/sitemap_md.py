"""Génère sitemap/SITEMAP.md : URL du sitemap Yoast + pages du site absentes du sitemap."""
import glob, json, os, re, sys, urllib.parse
OUT = sys.argv[1]; SM = os.path.join(OUT, 'sitemap')
pages = json.load(open(os.path.join(OUT, 'data', 'pages.json')))
n = lambda u: urllib.parse.unquote(u).lower().rstrip('/') + '/'
by = {n(p['url']): p for p in pages}
L = ['# Sitemap du site actuel', '',
     'Sitemap généré par Yoast SEO : https://rdv.levoyageanantes.fr/sitemap_index.xml (fichiers XML d\'origine dans ce dossier, avec `robots.txt`).', '']
idx = open(os.path.join(SM, 'sitemap_index.xml')).read()
subs = re.findall(r'<loc>[^<]*/([\w-]+-sitemap\.xml)</loc>\s*<lastmod>([^<]+)', idx)
in_sm, total, missing = set(), 0, []
L += ['| Sous-sitemap | Type WordPress | URL | Dernière modif. |', '| --- | --- | --- | --- |']
data = {}
for f, lm in subs:
    locs = re.findall(r'<loc>([^<]+)</loc>(?:\s*<lastmod>([^<]+))?', open(os.path.join(SM, f)).read())
    data[f] = locs; total += len(locs)
    L.append(f'| `{f}` | {f.replace("-sitemap.xml", "")} | {len(locs)} | {lm[:10]} |')
L += ['', f'**Total : {total} URL.**', '']
for f, locs in data.items():
    L += [f'## {f}', '']
    for u, lm in locs:
        in_sm.add(n(u)); p = by.get(n(u))
        if not p: missing.append(u)
        lien = f'[{p["fichier_md"]}](../{p["fichier_md"]})' if p else '⚠️ non extrait'
        L.append(f'- {urllib.parse.unquote(u)} — {lien}' + (f' — modif. {lm[:10]}' if lm else ''))
    L.append('')
absent = [p for p in pages if n(p['url']) not in in_sm]
L += ['## Pages du site absentes du sitemap', '',
      f'{len(absent)} pages existent sur le site mais ne sont pas dans le sitemap (types `page` et `shopping` exclus dans Yoast) :', '']
for p in sorted(absent, key=lambda p: p['fichier_md']):
    L.append(f'- {p["url"]} — [{p["fichier_md"]}](../{p["fichier_md"]})')
open(os.path.join(SM, 'SITEMAP.md'), 'w').write('\n'.join(L) + '\n')
print(total, 'URL sitemap,', len(missing), 'non extraites', missing, ';', len(absent), 'pages hors sitemap')
