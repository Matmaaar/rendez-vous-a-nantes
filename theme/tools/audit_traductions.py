"""Liste les textes EN/ES du contenu importé qui ne viennent pas du site actuel (textes nouveaux de la maquette).

Usage : python3 -I theme/tools/audit_traductions.py  ->  theme/TRADUCTIONS-A-RELIRE.md
"""
import html, json, os, re
ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
P = json.load(open(os.path.join(ROOT, 'contenu', 'data', 'pages.json')))
C = json.load(open(os.path.join(ROOT, 'theme', 'rdv-nantes', 'import', 'content.json')))

def norm(s):
    s = html.unescape(re.sub(r'<[^>]+>|[*_#>\[\]()|!¡¿?]', ' ', s or ''))
    return re.sub(r'\s+', ' ', s.replace('’', "'")).strip().lower()

corpus = {'en': '', 'es': ''}
for p in P:
    if p['langue'] in corpus:
        corpus[p['langue']] += ' ' + norm(' '.join([p.get('h1') or '', p.get('accroche') or '', p.get('main_complet_md') or '',
                                                   ' '.join(m['label'] for m in p.get('menu', []))]))
def from_site(text, lang):
    """Vrai si chaque phrase du texte figure telle quelle sur le site actuel."""
    phrases = [norm(x) for x in re.split(r'(?<=[.!?…])\s+', text) if norm(x)]
    return bool(phrases) and all(ph.rstrip('.') in corpus[lang] for ph in phrases)

SKIP = {'num', 'home', 'nav', 'post_type', 'map_x', 'map_y', 'maps', 'more', 'buy', 'img_alt', 'groups'}
LABELS = {'title': 'Titre', 'h1': 'Grand titre', 'lead': 'Chapô', 'tile_desc': 'Texte de tuile', 'amb': 'Ambiance', 'where': 'Localisation',
          'badges': 'Étiquettes', 'musts': 'Étapes', 'infos': 'Infos pratiques', 'subtitle': 'Sous-titre', 'link': 'Bouton', 'buy_label': 'Bouton d\'achat',
          'cta': 'Bouton', 'list_title': 'Titre de liste', 'link_label': 'Libellé des boutons', 'items': 'Éléments', 'after_title': 'Encadré : titre',
          'after_text': 'Encadré : texte', 'after_btn1': 'Encadré : bouton', 'after_btn2': 'Encadré : bouton', 'encart_title': 'Encadré : titre',
          'encart_text': 'Encadré : texte', 'encart_btn': 'Encadré : bouton', 'bonplan_label': 'Bandeau : pastille', 'bonplan_text': 'Bandeau : texte',
          'bonplan_btn': 'Bandeau : bouton', 'hero_title': 'Grand titre', 'hero_lead': 'Sous-titre', 'tiles_title': 'Titre des tuiles',
          'parcours_title': 'Parcours : titre', 'parcours_sub': 'Parcours : sous-titre', 'plans': 'Parcours : étapes', 'plan_foot': 'Parcours : texte'}

def pieces(v):
    out = []
    for line in v.split('\n'):
        for part in line.split('|'):
            t = re.sub(r'^#+\s*', '', part).strip()
            if len(t) > 2 and not t.startswith(('/', 'http')) and not re.fullmatch(r'[\d.,€ hkm·:]+', t):
                out.append(t)
    return out

L = ['# Traductions EN / ES à faire relire', '',
     'Textes du thème qui **n\'existent pas sur le site actuel** : ce sont des textes nouveaux de la maquette, traduits pour l\'import.',
     'Tout le reste du contenu EN / ES (fiches, quartiers, pages d\'infos, pages légales…) reprend le texte du site actuel.',
     'Dans les tableaux, « — » signifie que le texte de cette langue est repris du site actuel : rien à relire.',
     'Régénérer : `python3 -I theme/tools/audit_traductions.py`.', '']
total = 0
for it in C['items']:
    fr = it['langs'].get('fr')
    rows = []
    for k, frv in ([('title', fr['title'])] + list(fr['meta'].items())):
        if k in SKIP or not isinstance(frv, str) or not frv.strip():
            continue
        vals = {}
        for lang in ('en', 'es'):
            l = it['langs'].get(lang)
            if not l:
                continue
            v = l['title'] if k == 'title' else l['meta'].get(k, '')
            new = [x for x in pieces(v) if not from_site(x, lang)]
            if new:
                vals[lang] = ' / '.join(new)
        if vals:
            rows.append((LABELS.get(k, k), ' / '.join(pieces(frv)), vals.get('en', '—'), vals.get('es', '—')))
    if rows:
        L += [f'## {fr["title"]}', '', '| Champ | FR (maquette) | EN | ES |', '| --- | --- | --- | --- |']
        for r in rows:
            L.append('| ' + ' | '.join(x.replace('|', '/').replace('<br>', ' ') for x in r) + ' |')
            total += 1
        L.append('')
open(os.path.join(ROOT, 'theme', 'TRADUCTIONS-A-RELIRE.md'), 'w').write('\n'.join(L))
print(total, 'champs à relire')
