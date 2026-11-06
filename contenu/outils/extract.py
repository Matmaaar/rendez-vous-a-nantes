"""Extrait le contenu des pages HTML crawlées vers Markdown + JSON."""
import json, os, re, sys, urllib.parse
from bs4 import BeautifulSoup, Comment
from markdownify import markdownify as md

HTML_DIR, OUT = sys.argv[1], sys.argv[2]
BASE = 'https://rdv.levoyageanantes.fr'
ICONS = {'fa-map-marker-alt': 'Adresse', 'fa-tag': 'Tarifs', 'fa-phone-square': 'Téléphone',
         'fa-hourglass-half': 'Durée', 'fa-clock': 'Horaires'}

def tomd(node):
    s = md(str(node), heading_style='ATX', bullets='-', strip=['span'])
    s = re.sub(r'\n{3,}', '\n\n', s).strip()
    s = re.sub(r'[ \t]+\n', '\n', s)
    return s

def text(n):
    return re.sub(r'\s+', ' ', n.get_text(' ', strip=True)).strip() if n else ''

def lang_of(url):
    p = urllib.parse.urlsplit(url).path
    return 'en' if p.startswith('/en/') else 'es' if p.startswith('/es/') else 'fr'

def rel_path(url):
    p = urllib.parse.urlsplit(url).path.strip('/')
    lang = lang_of(url)
    if lang != 'fr': p = p[len(lang):].strip('/')
    p = re.sub(r'/+', '/', p)
    return lang, (p or 'accueil')

def extract(url, html):
    soup = BeautifulSoup(html, 'lxml')
    for c in soup.find_all(string=lambda t: isinstance(t, Comment)): c.extract()
    for t in soup(['script', 'style', 'noscript']): t.decompose()
    for i in soup.select('i.fa-check'): i.replace_with('✓')
    for i in soup.select('img.title-prefix'): i.decompose()
    for br in soup.select('td br, th br'): br.replace_with(' – ')
    head = soup.head
    meta = lambda **kw: (head.find('meta', attrs=kw) or {}).get('content', '') if head else ''
    d = {'url': url, 'langue': lang_of(url),
         'title': text(soup.title),
         'meta_description': meta(name='description'),
         'og_title': meta(property='og:title'), 'og_description': meta(property='og:description'),
         'og_image': meta(property='og:image'),
         'modifie_le': meta(property='article:modified_time')}
    body = soup.body or soup
    d['body_class'] = ' '.join(body.get('class', []))
    m = re.search(r'\b(page-template-[\w-]+|single-[\w-]+|postid-\d+|page-id-\d+)', d['body_class'])
    art = body.find('article')
    d['type'] = None
    if art:
        tc = [c for c in art.get('class', []) if c.startswith('type-')]
        d['type'] = tc[0][5:] if tc else None
        d['wp_id'] = art.get('id')
    # traductions (sélecteur WPML)
    d['traductions'] = {}
    for a in body.select('.wpml-ls a[hreflang]'):
        d['traductions'][a['hreflang']] = a['href']
    # menu latéral
    d['menu'] = [{'label': text(a.select_one('.leaf__label')), 'url': a['href'],
                  'icone': (a.find('img') or {}).get('src')} for a in body.select('.sidenav__content a')]
    main = body.find('main')
    if not main: return d
    # splash (accueil)
    splash = main.select_one('.splashscreen')
    if splash:
        bg = re.search(r"url\('([^']+)'\)", splash.get('style', ''))
        d['splash_image'] = bg.group(1) if bg else None
        bl = splash.select_one('.splashscreen__baseline')
        d['splash_texte'] = tomd(bl) if bl else ''
    # images slider
    d['images'] = [{'src': i.get('src'), 'alt': i.get('alt', '')} for i in main.select('.section__slider img')]
    # titre / accroche
    h1 = main.find('h1')
    d['h1'] = text(h1) if h1 and not splash else d.get('h1')
    bl = main.select_one('.page-content__baseline')
    d['accroche'] = text(bl)
    ac = main.select_one('.article-content')
    d['contenu_md'] = tomd(ac) if ac else ''
    # infos pratiques
    infos = []
    for c in main.select('.page-content__list-container'):
        ic = c.find('i'); cls = ic.get('class', []) if ic else []
        label = next((v for k, v in ICONS.items() if k in cls), 'Info')
        infos.append({'type': label, 'valeurs': [text(li) for li in c.select('li')]})
    d['infos_pratiques'] = infos
    # liste accordéon (pages rubriques)
    d['liste'] = []
    for a in main.select('.accordion a'):
        d['liste'].append({'numero': text(a.select_one('.accordion__leaf__number')),
                           'titre': text(a.select_one('.accordion__leaf__content__title')),
                           'description': text(a.select_one('.accordion__leaf__content__desc')),
                           'url': a.get('href')})
    # boutons bas de page
    d['liens'] = []
    for a in main.select('.page-footer a'):
        d['liens'].append({'label': text(a.select_one('.page-footer__button-label')), 'url': a.get('href'),
                           'title': a.get('title')})
    # autres blocs article-part (hors contenu principal et infos)
    extra = []
    for part in main.select('.article-part'):
        if part.select_one('.article-content') or part.select_one('.page-content__list-container'): continue
        extra.append(tomd(part))
    d['blocs_supplementaires_md'] = [e for e in extra if e]
    # rendu complet de <main> en markdown (filet de sécurité)
    mclone = BeautifulSoup(str(main), 'lxml')
    for sel in ['.section__slider', '.page-footer', '.splashscreen__logo', '.splashscreen__baseline-logos']:
        for n in mclone.select(sel): n.decompose()
    d['main_complet_md'] = tomd(mclone)
    return d

def frontmatter(d):
    keep = ['url', 'langue', 'type', 'title', 'meta_description', 'og_image', 'modifie_le']
    lines = ['---'] + [f'{k}: {json.dumps(d.get(k), ensure_ascii=False)}' for k in keep if d.get(k)]
    if d.get('traductions'):
        lines.append('traductions:')
        lines += [f'  {k}: {v}' for k, v in d['traductions'].items()]
    lines.append('---')
    return '\n'.join(lines)

def render_md(d):
    L = [frontmatter(d), '']
    if d.get('splash_texte') is not None and 'splash_image' in d:
        L += ['# Accueil', '', '## Texte d\'accueil', '', d['splash_texte'], '',
              f'Image de fond : {d.get("splash_image")}', '']
        if d.get('menu'):
            L += ['## Menu', ''] + [f'- [{m["label"]}]({m["url"]}) — icône : {m["icone"]}' for m in d['menu']] + ['']
    if d.get('h1'):
        L += [f'# {d["h1"]}', '']
    if d.get('accroche'): L += [f'> {d["accroche"]}', '']
    if d.get('contenu_md'): L += [d['contenu_md'], '']
    if d.get('infos_pratiques'):
        L += ['## Infos pratiques', '']
        for i in d['infos_pratiques']:
            L.append(f'- **{i["type"]}** : ' + ' / '.join(i['valeurs']))
        L.append('')
    for b in d.get('blocs_supplementaires_md', []): L += [b, '']
    if d.get('liste'):
        L += ['## Liste', '']
        for it in d['liste']:
            L.append(f'{it["numero"]}. [{it["titre"]}]({it["url"]}) — {it["description"]}')
        L.append('')
    if d.get('liens'):
        L += ['## Boutons / liens', '']
        L += [f'- {l["label"]}{" (" + l["title"] + ")" if l.get("title") else ""} → {l["url"]}' for l in d['liens']]
        L.append('')
    if d.get('images'):
        L += ['## Images (diaporama)', '']
        L += [f'- ![{i["alt"]}]({i["src"]})' for i in d['images']]
        L.append('')
    return re.sub(r'\n{3,}', '\n\n', '\n'.join(L)).strip() + '\n'

if __name__ == '__main__':
    idx = json.load(open(os.path.join(HTML_DIR, '_index.json')))
    allp = []
    for url, info in sorted(idx.items()):
        if 'file' not in info: continue
        fin = info.get('final', url)
        if fin != url and (fin in idx or fin.rstrip('/') + '/' in idx):
            print('doublon (redirection)', url, '->', fin); continue
        d = extract(url, open(os.path.join(HTML_DIR, info['file'])).read())
        lang, rp = rel_path(url)
        d['fichier_md'] = f'{lang}/{rp}.md'
        d['fichier_html'] = f'html-brut/{lang}/{rp}.html'
        for p, content in [(d['fichier_md'], render_md(d)), (d['fichier_html'], open(os.path.join(HTML_DIR, info['file'])).read())]:
            fp = os.path.join(OUT, p); os.makedirs(os.path.dirname(fp), exist_ok=True)
            open(fp, 'w').write(content)
        allp.append(d)
    os.makedirs(os.path.join(OUT, 'data'), exist_ok=True)
    json.dump(allp, open(os.path.join(OUT, 'data', 'pages.json'), 'w'), ensure_ascii=False, indent=1)
    print(len(allp), 'pages')
