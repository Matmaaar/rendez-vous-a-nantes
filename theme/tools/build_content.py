"""Construit theme/rdv-nantes/import/content.json.

FR : contenu de la maquette V1 (maquette.json, extrait du HTML de la maquette).
EN / ES : textes du site actuel (contenu/data/pages.json + contenu/html-brut), transposés
dans la structure de la maquette. Les textes courts propres à la maquette (accroches,
étiquettes, tuiles) sont traduits ici : à faire relire.

Usage : python3 -I theme/tools/build_content.py [maquette.json]  (par défaut theme/tools/maquette.json)
"""
import json, os, re, sys
from bs4 import BeautifulSoup

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
CONTENU = os.path.join(ROOT, 'contenu')
THEME = os.path.join(ROOT, 'theme', 'rdv-nantes')
B = 'https://rdv.levoyageanantes.fr'
M = json.load(open(sys.argv[1] if len(sys.argv) > 1 else os.path.join(os.path.dirname(os.path.abspath(__file__)), 'maquette.json')))
PAGES = {p['url']: p for p in json.load(open(os.path.join(CONTENU, 'data', 'pages.json')))}
MANIFEST = json.load(open(os.path.join(CONTENU, 'images', '_manifest.json')))
# Issus de l'export WordPress (contenu/outils/wxr_extract.py) : crédits photo et réglages Yoast.
CREDITS = json.load(open(os.path.join(CONTENU, 'images', '_credits.json')))
SEO = json.load(open(os.path.join(CONTENU, 'wordpress', 'seo.json')))
SEO_FRONT = {'fr': '/', 'en': '/en/rendez-vous-a-nantes/', 'es': '/es/rendez-vous-a-nantes/'}

def page(path):
    return PAGES[B + path]

def tr(fr_path, lang):
    """Page scrapée EN/ES correspondant à une page FR (via le sélecteur WPML)."""
    t = page(fr_path)['traductions'].get(lang)
    return PAGES.get(t) if t else None

def slug_of(url):
    return url.rstrip('/').rsplit('/', 1)[-1]

def article_html(p):
    """HTML du bloc .article-content d'une page scrapée, nettoyé pour l'éditeur."""
    html = open(os.path.join(CONTENU, p['fichier_html'])).read()
    soup = BeautifulSoup(html, 'lxml')
    node = soup.select_one('main .article-content')
    if not node:
        return ''
    for t in node.find_all(['img', 'script', 'style']):
        t.decompose()
    for t in node.find_all(True):
        for a in ['style', 'class', 'id', 'width', 'height', 'lang']:
            t.attrs.pop(a, None)
    for t in node.find_all('span'):
        t.unwrap()
    for a in node.find_all('a'):
        if not a.get_text(strip=True):
            a.decompose()
    out = node.decode_contents()
    out = out.replace('\xa0', ' ')
    out = re.sub(r'<p>\s*(&nbsp;|\s)*</p>', '', out)
    out = re.sub(r'\n{2,}', '\n', out).strip()
    return out

def rel(url):
    """URL du site actuel -> chemin interne FR (pour les liens résolus par le thème)."""
    return url.replace(B, '') if url and url.startswith(B) else url

# ---------------------------------------------------------------- correspondances
# id maquette -> slug FR du site actuel
LIEU_SLUGS = {
    'chateau-des-ducs-de-bretagne': 'le-chateau-des-ducs-de-bretagne', 'musee-darts': 'le-musee-darts',
    'quartier-bouffay': 'quartier-bouffay', 'le-lieu-unique': 'le-lieu-unique', 'jardin-des-plantes': 'le-jardin-des-plantes',
    'passage-pommeraye': 'le-passage-pommeraye', 'machines-de-lile': 'les-machines-de-lile',
    'parc-des-chantiers': 'quartier-creation-ile-nantes', 'quartier-chantenay': 'quartier-chantenay',
    'memorial-abolition-esclavage': 'le-memorial-de-labolition-de-lesclavage',
    'quartier-graslin': 'quartier-graslin', 'quartier-decre': 'uartier-decre', 'rue-dorleans': 'rue-dorleans',
    'marche-de-talensac': 'marche-de-talensac', 'bazar-officiel': 'bazar-officiel-voyage',
    'pass-nantes': 'pass-nantes', 'la-ligne-verte': 'la-ligne-verte', 'sur-le-chemin-des-plages': 'sur-le-chemin-des-plages',
}
# slugs corrigés dans la refonte (coquilles du site actuel)
SLUG_FIX = {'uartier-decre': 'quartier-decre', '1279': 'the-lieu-unique', 'mercato-detalensac': 'mercado-de-talensac'}
QUARTIER_SLUGS = {'ile-de-nantes': 'quartier-ile-de-nantes', 'graslin': 'quartier-graslin', 'bouffay': 'quartier-bouffay', 'guinguettes': 'les-guinguettes'}
CAT_BASE = {'incontournables': ('rdv_incontournable', 'incontournable'), 'shopping': ('rdv_shopping', 'shopping'), 'pass-nantes': ('rdv_pass', '24h-de-plus')}

def mockup_path(h):
    """Lien de la maquette (#/cat/id) -> chemin FR réel."""
    parts = h.lstrip('#/').split('/')
    if parts[0] == 'prendre-un-verre':
        return '/prendre-un-verre/%s/' % QUARTIER_SLUGS[parts[1]] if len(parts) > 1 else '/prendre-un-verre/'
    if parts[0] in CAT_BASE:
        base = CAT_BASE[parts[0]][1]
        return '/%s/%s/' % (base, SLUG_FIX.get(LIEU_SLUGS[parts[1]], LIEU_SLUGS[parts[1]])) if len(parts) > 1 else '/%s/' % base
    return {'aide': '/aide/', 'se-deplacer': '/access/'}.get(parts[0], '/' + parts[0] + '/')

INFO_LABELS = {
    'en': {'Adresse': 'Address', 'Tarifs': 'Prices', 'Téléphone': 'Phone', 'Durée': 'Duration', 'Horaires': 'Opening hours', 'Info': 'Info'},
    'es': {'Adresse': 'Dirección', 'Tarifs': 'Tarifas', 'Téléphone': 'Teléfono', 'Durée': 'Duración', 'Horaires': 'Horarios', 'Info': 'Info'},
}

def infos_lines(rows):
    return '\n'.join(' | '.join(str(x) if x is not True else '1' for x in r) for r in rows)

def scraped_infos(p, lang):
    rows = []
    for i in p.get('infos_pratiques', []):
        v = ' · '.join(x for x in i['valeurs'] if x)
        if v:
            rows.append([INFO_LABELS[lang].get(i['type'], i['type']), v])
    return infos_lines(rows)

def scraped_links(p):
    maps = more = ''
    for l in p.get('liens', []):
        u = l.get('url') or ''
        if 'google.' in u and '/maps' in u:
            maps = maps or u
        elif u.startswith('http') and B not in u and 'nantes-tourisme.com/en/getting' not in u:
            more = more or u
    return maps, more

ITEMS = []

def add(key, type_, order, langs, template='', image='', image_meta=None):
    ITEMS.append({'key': key, 'type': type_, 'template': template, 'order': order, 'image': image, 'image_meta': image_meta or {}, 'langs': langs})

# ---------------------------------------------------------------- textes traduits (maquette -> EN/ES)
T = {
    'cats': {  # id: (titre court, texte de tuile, grand titre) par langue
        'prendre-un-verre': {'en': ('Have a drink', 'Terraces, cocktails, pubs: four atmospheres', 'Have a drink'), 'es': ('Tomar una copa', 'Terrazas, cócteles, pubs: cuatro ambientes', 'Tomar una copa')},
        'manger': {'en': ('Eat', 'A selection of good Nantes restaurants', 'Eat'), 'es': ('Comer', 'Una selección de buenos restaurantes', 'Comer')},
        'incontournables': {'en': ('Unmissables', '10 good reasons to make the Voyage to Nantes', 'The unmissables'), 'es': ('Imprescindibles', '10 buenas razones para hacer el Voyage à Nantes', 'Los imprescindibles')},
        'footing': {'en': ('Jogging', 'From 5 to 15 km, for all levels', 'Jogging'), 'es': ('Footing', 'De 5 a 15 km, para todos los niveles', 'Footing')},
        'shopping': {'en': ('Shopping', 'Arcades, shopping streets and market', 'Shopping'), 'es': ('Shopping', 'Pasajes, calles comerciales y mercado', 'Shopping')},
        'se-deplacer': {'en': ('Getting around', 'Airport shuttle, tram, taxis', 'Getting around'), 'es': ('Desplazarse', 'Lanzadera aeropuerto, tranvía, taxis', 'Desplazarse')},
        'aide': {'en': ('Help', 'Le Voyage à Nantes visitor centre', 'Need a hand?'), 'es': ('Ayuda', 'La oficina del Voyage à Nantes', '¿Ayuda?')},
        'pass-nantes': {'en': ('Pass Nantes', 'From €27, from 24 hours to 7 days', 'Buy a City Pass'), 'es': ('Pass Nantes', 'Desde 27 €, de 24 h a 7 días', 'Comprar un City Pass')},
    },
}

# ---------------------------------------------------------------- rubriques
CAT_PAGE = {  # id maquette -> (slug FR, gabarit, type listé)
    'prendre-un-verre': ('prendre-un-verre', 'page-templates/rubrique-quartiers.php', 'rdv_quartier'),
    'manger': ('manger', 'page-templates/liste.php', ''),
    'incontournables': ('incontournable', 'page-templates/rubrique-lieux.php', 'rdv_incontournable'),
    'footing': ('footing', 'page-templates/liste.php', ''),
    'shopping': ('shopping', 'page-templates/rubrique-lieux.php', 'rdv_shopping'),
    'se-deplacer': ('access', 'page-templates/infos.php', ''),
    'aide': ('aide', 'page-templates/infos.php', ''),
    'pass-nantes': ('24h-de-plus', 'page-templates/rubrique-lieux.php', 'rdv_pass'),
}
LEADS = {
    'prendre-un-verre': {'en': 'Our selection of bars and unusual places for a drink in Nantes, depending on the atmosphere you are looking for.',
                         'es': 'Nuestra selección de bares y lugares insólitos para tomar una copa en Nantes, según el ambiente que busque.'},
    'incontournables': {'en': 'To make sure you don’t miss any of the destination’s must-sees, nothing could be simpler: in Nantes, just follow the green line drawn on the ground. A 22-kilometre trail bringing together the cultural stops, main monuments, works of art and tourist sites of the city.',
                        'es': 'Es fácil no perderse ninguno de los imprescindibles del destino. En Nantes, basta con seguir la línea verde marcada en el suelo. Un recorrido de 22 kilómetros que reúne los hitos culturales, los principales monumentos, las obras de arte y las atracciones turísticas de la ciudad.'},
    'shopping': {'en': 'Five addresses for shopping in Nantes, from the chic shops of the Graslin district to Le Voyage à Nantes souvenirs.',
                 'es': 'Cinco direcciones para ir de compras en Nantes, de las tiendas elegantes del barrio Graslin a los recuerdos del Voyage à Nantes.'},
    'pass-nantes': {'en': 'To find out all about Nantes, also see the tourist guide “Nantes, the permanent voyage”.',
                    'es': 'Para saberlo todo sobre Nantes, consulte también la guía turística «Nantes, el viaje permanente».'},
}
ENCART = {
    'fr': ('Pass Nantes, voyagez librement', 'En formule de 24 h à 7 jours, il donne accès à plus de 50 prestations dont les transports en commun, la navette aéroport, les musées et sites incontournables, les visites, les croisières sur l’Erdre, etc.', 'Découvrir le Pass Nantes'),
    'en': ('Pass Nantes, travel freely', 'Available from 24 hours to 7 days, it gives access to more than 50 services including public transport, the airport shuttle, museums and must-see sites, tours, cruises on the Erdre and more.', 'Discover the Pass Nantes'),
    'es': ('Pass Nantes, para viajar en libertad', 'Disponible de 24 horas a 7 días, da acceso a más de 50 servicios, como el transporte público, la lanzadera al aeropuerto, museos y lugares imprescindibles, visitas, cruceros por el Erdre y mucho más.', 'Descubrir el Pass Nantes'),
}

def cat_meta(c, lang):
    if lang == 'fr':
        title, tile, h1 = c['t'], c['d'], c['h']
        lead = c.get('lead', '')
    else:
        title, tile, h1 = T['cats'][c['id']][lang]
        lead = LEADS.get(c['id'], {}).get(lang, '')
    meta = {'num': c['n'], 'h1': h1, 'lead': lead, 'tile_desc': tile, 'home': '1' if c.get('home') else '0', 'nav': '1' if c.get('nav') else '0'}
    pt = CAT_PAGE[c['id']][2]
    if pt:
        meta['post_type'] = pt
    if c['id'] == 'incontournables':
        e = ENCART[lang]
        meta.update({'encart_title': e[0], 'encart_text': e[1], 'encart_btn': e[2] + ' | /24h-de-plus/pass-nantes/'})
    return title, meta

LISTES_TR = {
    'manger': {
        'en': {'lead': 'Looking for a great place to eat? The Les Tables de Nantes guide book lists over one hundred tested and approved addresses. Using its well thought out search engine, you will surely find the right place: eating with colleagues, vegetarian, big groups, quirky, outdoor seating, in the heart of town… For all tastes and budgets.',
               'cta': 'Visit lestablesdenantes.fr | https://www.lestablesdenantes.fr/en/', 'list_title': 'Some ideas for Monday evening dinner', 'link_label': 'See the listing',
               'after_title': 'Wine bars serving food', 'after_text': 'Also find on Les Tables de Nantes our selection of wine bars and cellars serving food and drinks.', 'after_btn1': 'See the selection | https://www.lestablesdenantes.fr/en/good-tips/wine-bars-dining-cellars/'},
        'es': {'lead': '¿Busca un buen restaurante? La guía Les Tables de Nantes reúne más de cien direcciones probadas y aprobadas. Gracias a su buscador, encontrará sin duda LA buena dirección: con compañeros, vegetariano, grupos, original, con terraza, en el centro… Para todos los gustos y presupuestos.',
               'cta': 'Consultar lestablesdenantes.fr | https://www.lestablesdenantes.fr/', 'list_title': 'Algunas ideas para la cena del lunes', 'link_label': 'Ver la ficha',
               'after_title': 'Bares de vinos', 'after_text': 'Encuentre también en Les Tables de Nantes nuestra selección de bares de vinos y bodegas donde comer.', 'after_btn1': 'Ver la selección | https://www.lestablesdenantes.fr/bons-plans/bars-a-vin/'},
    },
    'footing': {
        'en': {'lead': 'From 5 to 15 km, a selection of runs for all levels and all plans.', 'list_title': 'Our runs', 'link_label': 'See the run',
               'after_title': 'Go even further', 'after_text': 'Download the Runnin’City app and discover Nantes, audio-guided, original and sporty.'},
        'es': {'lead': 'De 5 a 15 km, una selección de recorridos para todos los niveles y todas las ganas.', 'list_title': 'Nuestros recorridos', 'link_label': 'Ver el recorrido',
               'after_title': 'Para ir más lejos', 'after_text': 'Descargue la aplicación Runnin’City para descubrir la ciudad de forma audioguiada, original y deportiva.'},
    },
}
FOOTING_META = {'en': ['10.6 km · our favourite', '6.5 km', '5.8 km', '12.5 km'], 'es': ['10,6 km · nuestro favorito', '6,5 km', '5,8 km', '12,5 km']}

def manger_items(lang):
    """Restaurants : textes du site actuel (EN/ES)."""
    p = tr('/manger/', lang)
    md = p['main_complet_md']
    items = []
    for m in re.finditer(r'- \[([^\]]+)\]\(([^)]+)\)\s*:\s*\*?\s*(.+?)\*?\s*(?=\n- \[|\n\n|$)', md, re.S):
        items.append([m.group(1).strip(), '', re.sub(r'\s+', ' ', m.group(3)).strip(' *'), m.group(2)])
    return items

def liste_meta(cid, lang):
    d = M['LISTES'][cid]
    if lang == 'fr':
        meta = {'lead': d['lead'], 'list_title': d['title'], 'link_label': d['linkLabel'],
                'items': '\n'.join(' | '.join([it['t'], it.get('m', ''), it.get('d', ''), it['u']]) for it in d['items'])}
        if d.get('cta'):
            meta['cta'] = d['cta'][0] + ' | ' + d['cta'][1]
        a = d['after']
        meta.update({'after_title': a['t'], 'after_text': a['d'], 'after_btn1': a['l'][0] + ' | ' + a['l'][1]})
        if a.get('l2'):
            meta['after_btn2'] = a['l2'][0] + ' | ' + a['l2'][1]
        return meta
    meta = dict(LISTES_TR[cid][lang])
    if cid == 'manger':
        meta['items'] = '\n'.join(' | '.join(i) for i in manger_items(lang))
    else:
        meta['items'] = '\n'.join(' | '.join([it['t'], FOOTING_META[lang][i], '', it['u']]) for i, it in enumerate(d['items']))
        a = d['after']
        meta['after_btn1'] = a['l'][0] + ' | ' + a['l'][1]
        meta['after_btn2'] = a['l2'][0] + ' | ' + a['l2'][1]
    return meta

INFOS_TR = {
    'se-deplacer': {'en': ('Airport shuttle, public transport, taxis: everything you need to get around during your stay.', [['Airport shuttle', '20-25 min to the centre'], ['Shuttle ticket', '€10'], ['Tram', 'Free at weekends', True]]),
                    'es': ('Lanzadera aeropuerto, transporte público, taxis: todo para desplazarse durante su estancia.', [['Lanzadera aeropuerto', '20-25 min hasta el centro'], ['Billete lanzadera', '10 €'], ['Tranvía', 'Gratis el fin de semana', True]])},
    'aide': {'en': ('Le Voyage à Nantes visitor centre advises you and books everything you need, 7 days a week.', [['Address', '9 rue des États, opposite the Château des ducs de Bretagne'], ['Phone', '0 892 464 044'], ['Email', 'info@levoyageanantes.fr']]),
             'es': ('La oficina del Voyage à Nantes le aconseja y le reserva todo lo que necesite, 7 días a la semana.', [['Dirección', '9 rue des États, frente al Castillo de los duques de Bretaña'], ['Teléfono', '0 892 464 044'], ['Correo', 'info@levoyageanantes.fr']])},
}

def infos_content_scraped(fr_path, lang):
    html = article_html(tr(fr_path, lang))
    # ancres de navigation interne de l'ancien site (« airport shuttle · public transport · taxis »)
    html = re.sub(r'^<p>(?:<a href="[^"]*#[^"]*">[^<]*</a>\s*[·|]?\s*)+</p>', '', html)
    html = html.replace('https://rdv.nantes-tourisme.com', B)
    return html

# ---------------------------------------------------------------- 1. accueil
PLAN_LABELS = {'en': ['1 h', '2 h', 'Half a day'], 'es': ['1 h', '2 h', 'Media jornada']}
PLAN_TR = {
    'en': {'Le Château des ducs de Bretagne': 'The Château des ducs de Bretagne', 'Quartier Bouffay': 'Bouffay district', 'Le Passage Pommeraye': 'Passage Pommeraye',
           'Le Mémorial de l’abolition de l’esclavage': 'The Memorial to the abolition of slavery', 'Les Machines de l’Île': 'The Machines de l’Île', 'Un verre au Hangar à bananes': 'A drink at the Hangar à bananes',
           'cour et remparts en accès libre': 'courtyard and ramparts free of charge', 'quartier médiéval, à 5 min': 'medieval quarter, 5 min away', 'galerie du XIXe siècle': '19th-century arcade',
           'quartier médiéval': 'medieval quarter', 'quai de la Fosse': 'quai de la Fosse', 'le musée d’histoire de Nantes': 'the Nantes history museum',
           'traversée vers l’Île de Nantes': 'across to the Île de Nantes', 'face à la Loire': 'facing the Loire'},
    'es': {'Le Château des ducs de Bretagne': 'El Castillo de los duques de Bretaña', 'Quartier Bouffay': 'Barrio Bouffay', 'Le Passage Pommeraye': 'El Passage Pommeraye',
           'Le Mémorial de l’abolition de l’esclavage': 'El Memorial de la abolición de la esclavitud', 'Les Machines de l’Île': 'Les Machines de l’Île', 'Un verre au Hangar à bananes': 'Una copa en el Hangar à bananes',
           'cour et remparts en accès libre': 'patio y murallas de acceso libre', 'quartier médiéval, à 5 min': 'barrio medieval, a 5 min', 'galerie du XIXe siècle': 'galería del siglo XIX',
           'quartier médiéval': 'barrio medieval', 'quai de la Fosse': 'quai de la Fosse', 'le musée d’histoire de Nantes': 'el museo de historia de Nantes',
           'traversée vers l’Île de Nantes': 'cruzando a la Île de Nantes', 'face à la Loire': 'frente al Loira'},
}
FRONT = {
    'fr': {'title': 'Accueil', 'slug': 'accueil', 'bonplan_label': 'Le bon plan', 'bonplan_text': 'Pass Nantes dès 27 € : 50 activités et les transports, de 24 h à 7 jours.', 'bonplan_btn': 'Découvrir le Pass Nantes | /24h-de-plus/pass-nantes/',
           'hero_title': 'Nantes en une heure<br>ou une demi-journée', 'hero_lead': 'Bars, tables, incontournables : notre sélection, sans détour.', 'tiles_title': 'Que voulez-vous faire ?',
           'parcours_title': 'Visiter Nantes à pied', 'parcours_sub': 'Un parcours tout prêt, selon le temps dont vous disposez.',
           'plan_foot': 'Ou laissez-vous guider : <a href="/24h-de-plus/la-ligne-verte/">suivez la ligne verte au sol</a>.'},
    'en': {'title': 'Home', 'slug': 'home', 'bonplan_label': 'Good deal', 'bonplan_text': 'Pass Nantes from €27: 50 activities and public transport, from 24 hours to 7 days.', 'bonplan_btn': 'Discover the Pass Nantes | /24h-de-plus/pass-nantes/',
           'hero_title': 'Nantes in an hour<br>or half a day', 'hero_lead': 'Bars, restaurants, must-sees: our selection, straight to the point.', 'tiles_title': 'What would you like to do?',
           'parcours_title': 'Explore Nantes on foot', 'parcours_sub': 'A ready-made route, depending on how much time you have.',
           'plan_foot': 'Or let yourself be guided: <a href="/24h-de-plus/la-ligne-verte/">follow the green line on the ground</a>.'},
    'es': {'title': 'Inicio', 'slug': 'inicio', 'bonplan_label': 'El buen plan', 'bonplan_text': 'Pass Nantes desde 27 €: 50 actividades y el transporte, de 24 h a 7 días.', 'bonplan_btn': 'Descubrir el Pass Nantes | /24h-de-plus/pass-nantes/',
           'hero_title': 'Nantes en una hora<br>o media jornada', 'hero_lead': 'Bares, restaurantes, imprescindibles: nuestra selección, sin rodeos.', 'tiles_title': '¿Qué quiere hacer?',
           'parcours_title': 'Visitar Nantes a pie', 'parcours_sub': 'Un recorrido listo, según el tiempo del que disponga.',
           'plan_foot': 'O déjese guiar: <a href="/24h-de-plus/la-ligne-verte/">siga la línea verde en el suelo</a>.'},
}

def plans(lang):
    out = []
    for i, p in enumerate(M['PLANS']):
        out.append('# ' + (p['label'] if lang == 'fr' else PLAN_LABELS[lang][i]))
        for t, s, h in p['items']:
            if lang != 'fr':
                t, s = PLAN_TR[lang].get(t, t), PLAN_TR[lang].get(s, s)
            out.append(' | '.join([t, s, mockup_path(h)]))
    return '\n'.join(out)

langs = {}
for lang, f in FRONT.items():
    meta = {k: v for k, v in f.items() if k not in ('title', 'slug')}
    meta['plans'] = plans(lang)
    langs[lang] = {'title': f['title'], 'slug': f['slug'], 'content': '', 'meta': meta, 'seo': SEO.get(SEO_FRONT[lang], {})}
add('accueil', 'page', 0, langs)

# ---------------------------------------------------------------- 2. rubriques
for order, c in enumerate(M['CATS'], start=1):
    slug_fr, tpl, _ = CAT_PAGE[c['id']]
    langs = {}
    for lang in ('fr', 'en', 'es'):
        title, meta = cat_meta(c, lang)
        content = ''
        if lang == 'fr':
            slug = slug_fr
            if c['type'] == 'infos':
                d = M['INFOS'][c['id']]
                meta.update({'lead': d['lead'], 'infos': infos_lines(d['infos'])})
                if d.get('maps'):
                    meta['maps'] = d['maps']
                content = d['html'].strip()
        else:
            sp = tr('/' + slug_fr + '/', lang)
            slug = slug_of(sp['url'])
            if c['type'] == 'infos':
                lead, infos = INFOS_TR[c['id']][lang]
                meta.update({'lead': lead, 'infos': infos_lines(infos)})
                if M['INFOS'][c['id']].get('maps'):
                    meta['maps'] = M['INFOS'][c['id']]['maps']
                content = infos_content_scraped('/' + slug_fr + '/', lang)
        if c['type'] == 'liste':
            meta.update(liste_meta(c['id'], lang))
        langs[lang] = {'title': title, 'slug': slug, 'content': content, 'meta': meta}
    add('rubrique-' + c['id'], 'page', order, langs, tpl)

# ---------------------------------------------------------------- 3. quartiers (prendre un verre)
Q_TR = {
    'ile-de-nantes': {
        'en': {'amb': 'Terraces facing the Loire', 'where': 'Île de Nantes · Hangar à bananes', 'badges': ['[X] min walk from the Cité des Congrès', 'Until 5.30 am'],
               'musts': [['La Cantine du Voyage', 'With its table football and deckchairs facing the Loire!', 'Dates 2024 to be updated'], ['Bar l’Absence', 'The bar of the School of Architecture, an artwork by Atelier Van Lieshout.']],
               'infos': [['Address', 'Quai des Antilles'], ['Atmosphere', 'Late night and buzzing'], ['Opening hours', 'Some bars open until 5.30 am', True]], 'group': 'A few suggestions'},
        'es': {'amb': 'Terrazas frente al Loira', 'where': 'Île de Nantes · Hangar à bananes', 'badges': ['A [X] min a pie de la Cité des Congrès', 'Hasta las 5.30 h'],
               'musts': [['La Cantine du Voyage', '¡Su futbolín y sus tumbonas con vistas al Loira!', 'Fechas 2024 por actualizar'], ['Bar l’Absence', 'La obra al pie de la Escuela de Arquitectura.']],
               'infos': [['Dirección', 'Quai des Antilles'], ['Ambiente', 'Nocturno y festivo'], ['Horarios', 'Algunos bares abren hasta las 5.30 h', True]], 'group': 'Algunas sugerencias'},
    },
    'graslin': {
        'en': {'amb': 'Chic and cosy cocktails', 'where': 'Graslin · around the theatre', 'badges': ['[X] min walk from the Cité des Congrès', 'Cocktail bars'],
               'infos': [['Where', 'Around the Graslin Theatre'], ['Atmosphere', 'Guaranteed chic and cosy'], ['On the menu', 'Wine bars, beer cellars, bars with music']], 'group': 'A few suggestions'},
        'es': {'amb': 'Cócteles elegantes y acogedores', 'where': 'Graslin · alrededor del teatro', 'badges': ['A [X] min a pie de la Cité des Congrès', 'Bares de cócteles'],
               'infos': [['Dónde', 'Alrededor del teatro Graslin'], ['Ambiente', 'El buen ambiente y la elegancia'], ['Programa', 'Vinoteca, cervecería, bar de baile']], 'group': 'Algunas sugerencias'},
    },
    'bouffay': {
        'en': {'amb': 'Terraces and lively pubs', 'where': 'Bouffay · medieval quarter', 'badges': ['[X] min walk from the Cité des Congrès', 'Match nights'],
               'infos': [['Where', 'Around the Place du Bouffay'], ['Atmosphere', 'Young and relaxed'], ['On the menu', 'Terraces, pubs, cocktail bars']], 'groups': ['A few suggestions', 'A few Irish pubs']},
        'es': {'amb': 'Terrazas y pubs animados', 'where': 'Bouffay · barrio medieval', 'badges': ['A [X] min a pie de la Cité des Congrès', 'Noches de partido'],
               'infos': [['Dónde', 'Alrededor de la plaza del Bouffay'], ['Ambiente', 'Joven y desenfadado'], ['Programa', 'Terrazas, pubs, bares de cócteles']], 'groups': ['Algunas sugerencias', 'Algunos Irish pubs']},
    },
    'guinguettes': {
        'en': {'amb': 'Guinguettes on sunny days', 'where': 'In town and nearby', 'badges': ['Seasonal', 'Several places'],
               'infos': [['Where', 'In town and nearby'], ['Atmosphere', 'Holiday mood'], ['When', 'On sunny days, depending on the place']], 'group': 'A few suggestions',
               'tags': ['Dates 2024 to be updated', 'Reopening 2025 to be updated', 'Reopening 2025 to be updated', 'Reopening 2025 to be updated']},
        'es': {'amb': 'Guinguettes con buen tiempo', 'where': 'En la ciudad y alrededores', 'badges': ['De temporada', 'Varios lugares'],
               'infos': [['Dónde', 'En la ciudad y alrededores'], ['Ambiente', 'Espíritu vacaciones'], ['Cuándo', 'Con buen tiempo, según el lugar']], 'group': 'Algunas sugerencias',
               'tags': ['Fechas 2024 por actualizar', 'Reapertura 2025 por actualizar', 'Reapertura 2025 por actualizar', 'Reapertura 2025 por actualizar']},
    },
}

def paragraphs(md):
    """Paragraphes de texte d'un contenu Markdown scrapé (hors listes et intertitres)."""
    out = []
    for block in re.split(r'\n\s*\n', md):
        b = block.strip()
        if not b or b.startswith('-') or b.startswith('#') or b.startswith('|') or b.startswith('['):
            continue
        t = re.sub(r'[*_]', '', b)
        t = re.sub(r'\[([^\]]+)\]\([^)]+\)', r'\1', t)
        t = re.sub(r'\s+', ' ', t).strip()
        if t.endswith(':') or len(t) < 25:
            continue
        out.append(t)
    return out

# Image à la une : 1re photo du diaporama du site actuel, sauf choix plus parlant.
IMAGE_SOURCE = {'/prendre-un-verre/quartier-bouffay/': '/incontournable/quartier-bouffay/'}
# Crédits remis en forme quand l'export n'a que le nom de fichier (contenu/images/_credits.json).
IMAGE_CREDIT = {
    'quartier-ile-de-nantes.jpg': '© Chama Chereau / Les Tables de Nantes',
    'les-guinguettes.jpg': 'Station Nuage, Yok Yok, Le Voyage à Nantes 2021 © Philippe Piron / LVAN',
}

def quartier_image(fr_path):
    """Copie (redimensionnée en JPEG 1600 px) la photo d'un quartier dans import/images."""
    from PIL import Image
    imgs = page(IMAGE_SOURCE.get(fr_path, fr_path)).get('images') or []
    if not imgs:
        return '', {}
    src = imgs[0]['src'].replace('rdv.nantes-tourisme.com', 'rdv.levoyageanantes.fr')
    local = MANIFEST.get(src)
    if not local:
        return '', {}
    name = fr_path.strip('/').split('/')[-1] + '.jpg'
    os.makedirs(os.path.join(THEME, 'import', 'images'), exist_ok=True)
    im = Image.open(os.path.join(CONTENU, local)).convert('RGB')
    im.thumbnail((1600, 1600))
    im.save(os.path.join(THEME, 'import', 'images', name), 'JPEG', quality=82, optimize=True)
    c = CREDITS.get(local, {})
    credit = IMAGE_CREDIT.get(name) or c.get('legende') or ''
    return name, {'alt': c.get('alt') or imgs[0].get('alt', ''), 'caption': credit}

def groups_text(groups):
    out = []
    for g in groups:
        out.append('## ' + g['t'])
        for b in g['bars']:
            out.append(' | '.join([b['n'], b.get('a', ''), b.get('tag', ''), b.get('d', '')]).rstrip(' |'))
    return '\n'.join(out)

for order, q in enumerate(M['QUARTIERS'], start=1):
    fr_path = '/prendre-un-verre/%s/' % QUARTIER_SLUGS[q['id']]
    common = {'num': q['n'], 'map_x': q.get('x', ''), 'map_y': q.get('y', '')}
    langs = {'fr': {'title': q['t'], 'slug': QUARTIER_SLUGS[q['id']], 'content': '', 'meta': dict(common, **{
        'amb': q['amb'], 'where': q['where'], 'img_alt': q['img'], 'badges': '\n'.join(q['badges']), 'lead': q['lead'],
        'musts': '\n'.join(' | '.join([m['t'], m['d'], m.get('tag', '')]).rstrip(' |') for m in q.get('musts', [])),
        'infos': infos_lines(q['infos']), 'groups': groups_text(q['groups']), 'note': q.get('note', '')})}}
    for lang in ('en', 'es'):
        sp = tr(fr_path, lang)
        t = Q_TR[q['id']][lang]
        paras = paragraphs(sp['contenu_md'])
        # bars : mêmes établissements que la maquette FR, titres de groupes traduits
        names = t.get('groups') or [t['group']] * len(q['groups'])
        groups = []
        for gi, g in enumerate(q['groups']):
            bars = []
            for bi, b in enumerate(g['bars']):
                tag = t['tags'][bi] if t.get('tags') else ''
                bars.append({'n': b['n'], 'a': b.get('a', ''), 'tag': tag})
            groups.append({'t': names[gi], 'bars': bars})
        note = ''
        if q['id'] == 'bouffay':
            note = paras[-1] if len(paras) > 1 else ''
        langs[lang] = {'title': sp['h1'], 'slug': SLUG_FIX.get(slug_of(sp['url']), slug_of(sp['url'])), 'content': '', 'meta': dict(common, **{
            'amb': t['amb'], 'where': t['where'], 'img_alt': '', 'badges': '\n'.join(t['badges']), 'lead': paras[0] if paras else '',
            'musts': '\n'.join(' | '.join(m).rstrip(' |') for m in t.get('musts', [])),
            'infos': infos_lines(t['infos']), 'groups': groups_text(groups), 'note': note})}
    img, img_meta = quartier_image(fr_path)
    add('quartier-' + q['id'], 'rdv_quartier', order, langs, image=img, image_meta=img_meta)

# ---------------------------------------------------------------- 4. fiches lieux
LINK_TR = {'Prendre un verre au Bouffay': {'en': 'Have a drink in Bouffay', 'es': 'Tomar una copa en Bouffay'},
           'Prendre un verre sur l’Île de Nantes': {'en': 'Have a drink on the Île de Nantes', 'es': 'Tomar una copa en la Île de Nantes'}}
PASS_HTML = {
    'en': {'pass-nantes': '''<p>The Pass Nantes gives free admission to more than 50 sights and sites and includes public transport, the airport shuttle included.</p>
<p>Free admission: all the museums and exhibitions, the Machines de l’Île, public transport, river cruises, bus tours, visits…</p>
<h2>24h stay example</h2>
<table class="ptable"><thead><tr><th>Activity</th><th class="r">Price</th></tr></thead><tbody>
<tr><td>24h TAN ticket (public transport)</td><td class="r">€6.50</td></tr>
<tr><td>Castle of the Dukes of Brittany: museum + audioguide</td><td class="r">€11</td></tr>
<tr><td>River cruise on the Erdre</td><td class="r">€16.50</td></tr>
<tr><td>Machines de l’île: Galerie des Machines</td><td class="r">€11</td></tr>
<tr><td>Machines de l’île: Carrousel des Mondes Marins</td><td class="r">€9</td></tr>
<tr class="tot"><td>Without the Pass Nantes</td><td class="r">€54</td></tr>
<tr class="tot"><td>With the 24h Pass Nantes</td><td class="r">€27</td></tr>
</tbody></table>
<h2>Prices</h2>
<ul><li>Pass 24h: €27</li><li>Pass 48h: €37</li><li>Pass 72h: €45</li><li>Pass 7 days: €85</li></ul>
<p><a href="https://www.nantes-tourisme.com/en/visit/nantes-pass">Everything you need to know about the Pass Nantes</a></p>''',
           'sur-le-chemin-des-plages': None},
    'es': {'pass-nantes': '''<p>Durante un día, un fin de semana, un puente o incluso una semana, viaje a su ritmo. El Pass Nantes le da acceso a 50 actividades imprescindibles: museos, exposiciones, visitas guiadas, cruceros, degustaciones y transportes, incluida la lanzadera al aeropuerto.</p>
<h2>Ejemplo de estancia de 24 h</h2>
<table class="ptable"><thead><tr><th>Actividad</th><th class="r">Precio</th></tr></thead><tbody>
<tr><td>Billete TAN 24 h (transporte público)</td><td class="r">6,50 €</td></tr>
<tr><td>Castillo de los duques de Bretaña: museo + audioguía</td><td class="r">11 €</td></tr>
<tr><td>Crucero por el Erdre</td><td class="r">16,50 €</td></tr>
<tr><td>Machines de l’île: Galerie des Machines</td><td class="r">11 €</td></tr>
<tr><td>Machines de l’île: Carrousel des Mondes Marins</td><td class="r">9 €</td></tr>
<tr class="tot"><td>Sin el Pass Nantes</td><td class="r">54 €</td></tr>
<tr class="tot"><td>Con el Pass Nantes 24 h</td><td class="r">27 €</td></tr>
</tbody></table>
<h2>Tarifas</h2>
<ul><li>Pass 24 h: 27 €</li><li>Pass 48 h: 37 €</li><li>Pass 72 h: 45 €</li><li>Pass 7 días: 85 €</li></ul>
<p><a href="https://www.levoyageanantes.fr/es/hacer/pass-nantes/">Ver todos los servicios del Pass Nantes</a></p>''',
           'sur-le-chemin-des-plages': None},
}
PASS_TR = {
    'pass-nantes': {'en': ('For 1, 2, 3 or 7 days, all the must-sees in Nantes at the best price!', [['Duration', 'From 24 hours to 7 days'], ['From', '€27'], ['Included', '50 activities and public transport', True]], 'Buy online'),
                    'es': ('Durante un día, un fin de semana o una semana, viaje a su ritmo.', [['Duración', 'De 24 h a 7 días'], ['Desde', '27 €'], ['Incluido', '50 actividades y el transporte público', True]], 'Comprar en línea')},
    'la-ligne-verte': {'en': ('The essence of the city, without map or guide', [['Length', '22 km'], ['Price', 'Free']], ''),
                       'es': ('Lo esencial de la ciudad, sin plano ni guía', [['Longitud', '22 km'], ['Precio', 'Gratis']], '')},
    'sur-le-chemin-des-plages': {'en': ('The Loire, the estuary and the ocean less than an hour away', [['Cruise duration', '2 h 45'], ['Season', 'April to October'], ['Departure', 'From Nantes or Saint-Nazaire']], 'Book the cruise'),
                                 'es': ('El Loira, el estuario y el océano a menos de una hora', [['Duración del crucero', '2 h 45'], ['Temporada', 'De abril a octubre'], ['Salida', 'Desde Nantes o Saint-Nazaire']], 'Reservar el crucero')},
}

for cid, (ptype, base) in CAT_BASE.items():
    for order, l in enumerate(M['LIEUX'][cid], start=1):
        fr_slug = LIEU_SLUGS[l['id']]
        fr_path = '/%s/%s/' % (base, fr_slug)
        content = l.get('html') or '<p>%s</p>' % l['txt']
        meta = {'subtitle': l['s'], 'infos': infos_lines(l['infos']), 'maps': l.get('maps', ''), 'more': l.get('more', ''), 'buy': l.get('buy', ''), 'buy_label': l.get('buyLabel', '')}
        if l.get('link'):
            meta['link'] = l['link'][0] + ' | ' + mockup_path(l['link'][1])
        langs = {'fr': {'title': l['t'], 'slug': SLUG_FIX.get(fr_slug, fr_slug), 'content': content.strip(), 'meta': meta}}
        for lang in ('en', 'es'):
            sp = tr(fr_path, lang)
            if not sp:
                continue
            maps, more = scraped_links(sp)
            m2 = {'subtitle': sp.get('accroche', ''), 'infos': scraped_infos(sp, lang), 'maps': maps or meta['maps'], 'more': more, 'buy': '', 'buy_label': ''}
            body = article_html(sp)
            if cid == 'pass-nantes':
                sub, infos, buy_label = PASS_TR[l['id']][lang]
                m2.update({'subtitle': sub, 'infos': infos_lines(infos), 'buy': meta['buy'], 'buy_label': buy_label if meta['buy'] else ''})
                if PASS_HTML[lang].get(l['id']):
                    body = PASS_HTML[lang][l['id']]
            if l.get('link'):
                m2['link'] = LINK_TR[l['link'][0]][lang] + ' | ' + mockup_path(l['link'][1])
            s = slug_of(sp['url'])
            langs[lang] = {'title': sp['h1'], 'slug': SLUG_FIX.get(s, s), 'content': body, 'meta': m2}
        add('%s-%s' % (cid, l['id']), ptype, order, langs)

# ---------------------------------------------------------------- 5. pages légales
for key, fr_path in (('mentions-legales', '/mentions-legales/'), ('cookies', '/cookiespolicy/')):
    langs = {}
    for lang in ('fr', 'en', 'es'):
        sp = page(fr_path) if lang == 'fr' else tr(fr_path, lang)
        if not sp:
            continue
        langs[lang] = {'title': sp['h1'] or sp['title'].split(' - ')[0], 'slug': slug_of(sp['url']), 'content': article_html(sp), 'meta': {},
                       'seo': SEO.get(sp['url'].replace(B, ''), {})}
    add(key, 'page', 90, langs)

out = os.path.join(THEME, 'import', 'content.json')
json.dump({'front': 'accueil', 'items': ITEMS}, open(out, 'w'), ensure_ascii=False, indent=1)
print(len(ITEMS), 'éléments ->', out)
