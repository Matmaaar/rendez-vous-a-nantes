import json, os, sys
OUT = sys.argv[1]
pages = json.load(open(os.path.join(OUT, 'data', 'pages.json')))
by_url = {p['url']: p for p in pages}
def norm(u): return (u or '').rstrip('/') + '/'
by_norm = {norm(p['url']): p for p in pages}
rows, seen = [], set()
order = {'fr': 0, 'en': 1, 'es': 2}
for p in sorted(pages, key=lambda p: (order[p['langue']], p['fichier_md'])):
    if p['url'] in seen: continue
    group = {}
    for lang, u in (p.get('traductions') or {}).items():
        q = by_norm.get(norm(u))
        if q and q['langue'] == lang: group[lang] = q
    group.setdefault(p['langue'], p)
    for q in group.values(): seen.add(q['url'])
    rows.append(group)
def cell(q):
    if not q: return '—'
    t = q.get('h1') or ('Accueil' if q.get('splash_texte') is not None else q['title'].split(' - ')[0])
    return f'[{t}]({q["fichier_md"]})'
def section(g):
    p = g.get('fr') or g.get('en') or g.get('es')
    return p['fichier_md'].split('/', 1)[1].split('/')[0].replace('.md', '') if '/' in p['fichier_md'].split('/', 1)[1] else '(pages)'
L = ['# Plan du site & correspondance des traductions', '',
     'Chaque ligne = un même contenu dans les 3 langues (d\'après le sélecteur de langue WPML du site). « — » = pas de traduction.', '',
     '| Type | FR | EN | ES |', '| --- | --- | --- | --- |']
for g in sorted(rows, key=lambda g: (g.get('fr') or g.get('en') or g.get('es'))['fichier_md'].split('/',1)[1]):
    p = g.get('fr') or g.get('en') or g.get('es')
    L.append(f'| {p.get("type") or "page"} | {cell(g.get("fr"))} | {cell(g.get("en"))} | {cell(g.get("es"))} |')
open(os.path.join(OUT, 'PLAN-DU-SITE.md'), 'w').write('\n'.join(L) + '\n')
from collections import Counter
print(len(rows), 'groupes', Counter(p['langue'] for p in pages))
