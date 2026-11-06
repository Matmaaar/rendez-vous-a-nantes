"""Exploite l'export WordPress (WXR) et l'export ACF du site actuel.

Produit, sans recopier de données personnelles (comptes, emails) :
- contenu/wordpress/README.md        inventaire, structure ACF, SEO, constats
- contenu/wordpress/non-publies/      contenus en attente / brouillons (non visibles sur le site)
- contenu/images/_credits.json        texte alternatif, légende (crédit) et titre des images
- theme/redirections.csv              redirections 301 à créer sur le nouveau site (plugin Redirection)

Usage : python3 -I contenu/outils/wxr_extract.py <export.xml> <acf-export.json>
"""
import json, os, re, sys, collections, urllib.parse
import xml.etree.ElementTree as ET
from markdownify import markdownify as md

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
CONTENU = os.path.join(ROOT, 'contenu')
OUT = os.path.join(CONTENU, 'wordpress')
B = 'https://rdv.levoyageanantes.fr'
NS = {'wp': 'http://wordpress.org/export/1.2/', 'content': 'http://purl.org/rss/1.0/modules/content/', 'excerpt': 'http://wordpress.org/export/1.2/excerpt/'}
SKIP_TYPES = {'rugby2023'}  # JO 2024 / Rugby 2023 : hors refonte

def parse(path):
    ch = ET.parse(path).getroot().find('channel')
    items = []
    for it in ch.findall('item'):
        g = lambda t: it.findtext(t, namespaces=NS) or ''
        meta = {}
        for m in it.findall('wp:postmeta', NS):
            meta.setdefault(m.findtext('wp:meta_key', namespaces=NS), m.findtext('wp:meta_value', namespaces=NS) or '')
        items.append({
            'id': int(g('wp:post_id')), 'type': g('wp:post_type'), 'status': g('wp:status'), 'title': it.findtext('title') or '',
            'slug': urllib.parse.unquote(g('wp:post_name')), 'order': int(g('wp:menu_order') or 0), 'link': it.findtext('link') or '',
            'date': g('wp:post_date'), 'content': g('content:encoded'), 'excerpt': g('excerpt:encoded'), 'url': g('wp:attachment_url'),
            'meta': meta, 'cats': [(c.get('domain'), c.text) for c in it.findall('category')],
        })
    return items

def fix(s):
    """Répare le texte doublement encodé (UTF-8 lu comme Latin-1) présent dans certains titres."""
    if re.search('[ÃÂâ][\x80-\xbf]|Ã', s or ''):
        try:
            return s.encode('latin1').decode('utf8')
        except (UnicodeDecodeError, UnicodeEncodeError):
            return s
    return s

def lang(i):
    l = i['link']
    return 'en' if '/en/' in l else 'es' if '/es/' in l else 'fr'

def path_of(url):
    return url.replace(B, '') or '/'

def to_md(html):
    return re.sub(r'\n{3,}', '\n\n', md(html or '', heading_style='ATX', bullets='-')).strip()

def php_ids(serialized):
    return [int(x) for x in re.findall(r's:\d+:"(\d+)"', serialized or '')]

items = parse(sys.argv[1])
acf = json.load(open(sys.argv[2]))
by_id = {i['id']: i for i in items}
os.makedirs(os.path.join(OUT, 'non-publies'), exist_ok=True)

# ------------------------------------------------------------- crédits images
att = [i for i in items if i['type'] == 'attachment']
manifest = json.load(open(os.path.join(CONTENU, 'images', '_manifest.json')))

def stem(name):
    name = urllib.parse.unquote(name.rsplit('/', 1)[-1])
    return re.sub(r'(-scaled|-\d+x\d+)+(?=\.\w+$)', '', name).lower()

att_by_stem = {}
for a in att:
    # plusieurs médias peuvent porter le même nom de fichier : on garde celui qui a une légende ou un alt
    prev = att_by_stem.get(stem(a['url']))
    if not prev or (not prev['excerpt'].strip() and (a['excerpt'].strip() or a['meta'].get('_wp_attachment_image_alt'))):
        att_by_stem[stem(a['url'])] = a
credits = {}
for url, local in sorted(manifest.items()):
    if not local or '/theme/' in local:
        continue
    a = att_by_stem.get(stem(url))
    if not a:
        continue
    entry = {k: v for k, v in {
        'alt': a['meta'].get('_wp_attachment_image_alt', '').strip(),
        'legende': fix(re.sub(r'\s+', ' ', a['excerpt']).strip()),
        'titre': fix(a['title'].strip()),
    }.items() if v}
    if entry:
        credits[local] = entry
json.dump(credits, open(os.path.join(CONTENU, 'images', '_credits.json'), 'w'), ensure_ascii=False, indent=1)

# ------------------------------------------------------------- non publiés
FIELDS = {
    'lieu_verre': ('lieu_', 'Prendre un verre'), '24h': ('24h_', 'Pass Nantes (24h de +)'),
    'incontournable': ('inc_', 'Incontournables'), 'shopping': ('shopping_', 'Shopping'),
}
unpublished = [i for i in items if i['type'] in FIELDS and i['status'] in ('draft', 'pending', 'private', 'future')]
for i in unpublished:
    pre, rub = FIELDS[i['type']]
    m = i['meta']
    cat = ', '.join(c[1] for c in i['cats'] if c[0] and c[0].startswith('category_'))
    photos = [by_id[p]['url'] for p in php_ids(m.get(pre + 'photos_desktop')) if p in by_id]
    lines = ['---', f'statut: {i["status"]}', f'type: {i["type"]}', f'langue: {lang(i)}', f'derniere_modif: {i["date"][:10]}']
    if cat:
        lines.append(f'categorie: "{cat}"')
    lines += ['---', '', f'# {i["title"]}', '', f'*Rubrique : {rub}. Non publié sur le site actuel.*', '']
    if m.get(pre + 'baseline'):
        lines += [f'> {m[pre + "baseline"]}', '']
    desc = m.get(pre + 'description') or i['content']
    if desc:
        lines += [to_md(desc), '']
    info = [(lbl, m.get(pre + k)) for lbl, k in (('Adresse', 'adresse'), ('Itinéraire', 'itineraire'), ('+ d\'infos', 'more'))]
    info = [(lbl, v) for lbl, v in info if v]
    if info:
        lines += ['## Infos', ''] + [f'- **{lbl}** : {v}' for lbl, v in info] + ['']
    if photos:
        lines += ['## Photos', ''] + [f'- {p}' for p in photos] + ['']
    fn = f'{i["type"]}-{lang(i)}-{i["slug"] or i["id"]}.md'
    open(os.path.join(OUT, 'non-publies', fn), 'w').write('\n'.join(lines).rstrip() + '\n')

# ------------------------------------------------------------- redirections
redirects = []
def add(src, dst, regex=0):
    if src != dst and (src, dst) not in [(r[0], r[1]) for r in redirects]:
        redirects.append((src, dst, regex))

# 1. anciens slugs connus de WordPress (perdus lors d'une migration)
OLD = collections.defaultdict(list)
tree = ET.parse(sys.argv[1]).getroot().find('channel')
for it in tree.findall('item'):
    pid = int(it.findtext('wp:post_id', namespaces=NS))
    for m in it.findall('wp:postmeta', NS):
        if m.findtext('wp:meta_key', namespaces=NS) == '_wp_old_slug':
            OLD[pid].append(urllib.parse.unquote(m.findtext('wp:meta_value', namespaces=NS) or ''))
NEW_SLUG = {'/shopping/uartier-decre/': '/shopping/quartier-decre/', '/en/unmissable/1279/': '/en/unmissable/the-lieu-unique/',
            '/es/shopping/mercato-detalensac/': '/es/shopping/mercado-de-talensac/'}
for pid, olds in OLD.items():
    i = by_id[pid]
    if i['status'] != 'publish' or i['type'] in SKIP_TYPES or i['type'] not in ('page', '24h', 'incontournable', 'lieu_verre', 'shopping'):
        continue
    cur = path_of(i['link'])
    target = NEW_SLUG.get(cur, cur)
    parent = cur.rstrip('/').rsplit('/', 1)[0] + '/'
    for o in olds:
        if o and o != i['slug']:
            add(parent + o + '/', target)
# 2. slugs corrigés dans la refonte
for src, dst in NEW_SLUG.items():
    add(src, dst)
# 3. pages événementielles non reprises -> accueil de la langue
for i in items:
    if i['status'] == 'publish' and i['type'] == 'page' and i['meta'].get('_wp_page_template') == 'template-home.php':
        p = path_of(i['link'])
        if p.strip('/') not in ('', 'en', 'es'):
            add(p, '/' + (lang(i) + '/' if lang(i) != 'fr' else ''))
add('^/jo-paris-2024/.*', '/', 1)
add('^/en/jo-paris-2024/.*', '/en/', 1)
add('^/es/jo-paris-2024/.*', '/es/', 1)
# 4. lien cassé présent sur le site actuel
add('/24h-mas/', '/es/24h-mas/')
redirects.sort(key=lambda r: (r[2], r[0]))
with open(os.path.join(ROOT, 'theme', 'redirections.csv'), 'w') as f:
    f.write('source,target,regex,code\n')
    for s, d, rx in redirects:
        f.write(f'{s},{d},{rx},301\n')

# ------------------------------------------------------------- SEO (Yoast) réutilisé par l'import du thème
seo = {}
for i in items:
    if i['status'] != 'publish' or i['type'] in SKIP_TYPES:
        continue
    vals = {k[len('_yoast_wpseo_'):]: v for k, v in i['meta'].items()
            if k in ('_yoast_wpseo_title', '_yoast_wpseo_metadesc', '_yoast_wpseo_meta-robots-noindex') and v}
    if vals:
        seo[path_of(i['link'])] = vals
json.dump(seo, open(os.path.join(OUT, 'seo.json'), 'w'), ensure_ascii=False, indent=1)

# ------------------------------------------------------------- README
cnt = collections.Counter((i['type'], i['status']) for i in items)
L = ['# Export WordPress du site actuel', '',
     'Synthèse de l\'export WordPress (WXR, 06/11/2026) et de l\'export des champs ACF fournis par l\'équipe.',
     'Les fichiers bruts ne sont **pas** versionnés (le repo est public et l\'export contient les comptes utilisateurs).',
     'Régénération : `python3 -I contenu/outils/wxr_extract.py <export.xml> <acf-export.json>`.', '',
     '## Inventaire', '', '| Type | Statut | Nombre |', '| --- | --- | --- |']
for (t, s), n in sorted(cnt.items()):
    L.append(f'| {t} | {s} | {n} |')
pend = collections.Counter((i['type'], i['status'], lang(i)) for i in unpublished)
L += ['', '## Contenus non publiés', '',
      'Absents du site public, donc absents du crawl : ils sont dans [`non-publies/`](non-publies/).', '']
L += [f'- {t} / {s} / {l} : {n}' for (t, s, l), n in sorted(pend.items())]
L += ['', '- **22 fiches « Prendre un verre » en attente (2019)** : ancienne version de la rubrique, une fiche par lieu, classées « Santé » / « Pour les couche-tard » (Lieu Unique, L\'Absence, Hangar à Bananes, Cantine du Voyage, Bateau Lavoir, Warehouse, Floride, L\'Éléphant, Conclav, Petit Marais). Remplacées par les 4 fiches quartiers.',
      '- **6 brouillons Pass Nantes / 24h de + (2023)** : « Le Voyage à Nantes, l\'événement estival » et « Visites privées réservables en ligne » (ES jamais traduit).',
      '', 'Aucun de ces contenus n\'est importé dans le nouveau thème.', '']
L += ['## Champs ACF du site actuel → nouveau thème', '', '| Groupe ACF (actif) | Champs | Équivalent dans le thème |', '| --- | --- | --- |']
EQUIV = {
    'Incontournables & Rugby/JO': 'Fiche : sous-titre, contenu, infos pratiques (adresse, tarifs, petits plus), Itinéraire, En savoir plus',
    'Prendre un verre': 'Quartier : chapô, contenu, infos pratiques, bars',
    'Fiche Shopping': 'Fiche : sous-titre, contenu, infos pratiques, Itinéraire, En savoir plus',
    'Fiche 24h de +': 'Fiche : contenu, En savoir plus, bouton d\'achat',
    'Manger - 3 coups de pouce': 'Page « liste d\'adresses » : éléments (Titre | précision | description | lien)',
    'Home & landings': 'Accueil : champs « Page d\'accueil » (pas d\'image de fond dans la maquette)',
    'Options - Menus': 'Rubriques : cases « menu » et « accueil », ordre des pages (pas d\'icônes dans la maquette)',
    'Page classique': 'Pages d\'infos : chapô',
    'Options - Incontournables': 'Page de rubrique : chapô / contenu',
    'Options - 24h de +': 'Page de rubrique : chapô / contenu',
    'Options - Home & landings': 'Accueil (la vidéo YouTube n\'est pas reprise par la maquette)',
}
for g in acf:
    if not g.get('active'):
        continue
    names = []
    def walk(fs):
        for f in fs:
            if f.get('name'):
                names.append(f['name'])
            walk(f.get('sub_fields') or [])
    walk(g['fields'])
    eq = EQUIV.get(g['title'], 'Non repris (photos / style / JO)' if any(x in g['title'] for x in ('JO', 'Style', 'Shopping', 'Prendre un verre')) else '—')
    if g['title'] == 'Prendre un verre':
        eq = EQUIV['Prendre un verre']
    L.append(f'| {g["title"]} | {", ".join("`" + n + "`" for n in names)} | {eq} |')
L += ['', 'Les **galeries photos** (mobile / desktop) de chaque fiche ne sont pas affichées par la maquette ; les photos restent disponibles dans [`../images/`](../images/).', '',
      '## SEO (Yoast)', '',
      '- Titre et meta description personnalisés uniquement sur l\'accueil (FR, EN, ES) — repris par l\'import du thème.',
      '- `noindex` sur les mentions légales (FR, EN) — repris.',
      '- Les autres pages utilisent les réglages Yoast par défaut. Le site entier est actuellement en `noindex, nofollow`.', '',
      '## Images', '',
      f'- {len(att)} médias ; {sum(1 for a in att if a["meta"].get("_wp_attachment_image_alt"))} avec texte alternatif, {sum(1 for a in att if a["excerpt"].strip())} avec légende (souvent le crédit « © Photographe / LVAN »).',
      f'- [`../images/_credits.json`](../images/_credits.json) : alt / légende / titre pour {len(credits)} des images téléchargées.', '',
      '## Redirections', '',
      'Le plugin Redirection ne contient aucune règle. [`../../theme/redirections.csv`](../../theme/redirections.csv) liste les redirections 301 à créer sur le nouveau site '
      f'({len(redirects)} règles) : anciens slugs mémorisés par WordPress, slugs corrigés, pages événementielles et JO 2024 non reprises.', '']
open(os.path.join(OUT, 'README.md'), 'w').write('\n'.join(L))
print(len(unpublished), 'non publiés |', len(credits), 'crédits |', len(redirects), 'redirections')
