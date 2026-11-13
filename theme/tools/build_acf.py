"""Génère les groupes de champs ACF du thème (theme/rdv-nantes/acf-json/*.json).

ACF lit ces fichiers tout seul (« Local JSON ») : les cases apparaissent dans l'admin dès qu'ACF PRO est actif.
Une case ajoutée ou modifiée ensuite dans l'admin (ACF → Groupes de champs) est réenregistrée dans ces fichiers.

Usage : python3 -I theme/tools/build_acf.py
"""
import json, os, time

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(ROOT, 'rdv-nantes', 'acf-json')

# Préférences de traduction WPML (extension ACF Multilingual) : 1 = copier, 2 = traduire.
COPY, TRANSLATE = 1, 2

TEMPLATES = {
    'rubrique-quartiers': 'page-templates/rubrique-quartiers.php',
    'rubrique-lieux': 'page-templates/rubrique-lieux.php',
    'liste': 'page-templates/liste.php',
    'infos': 'page-templates/infos.php',
}
POST_TYPES = {
    'rdv_incontournable': 'Incontournables',
    'rdv_shopping': 'Shopping',
    'rdv_pass': 'Pass Nantes',
    'rdv_quartier': 'Quartiers (Prendre un verre)',
}


def f(name, label, ftype='text', help='', **kw):
    return dict(name=name, label=label, type=ftype, instructions=help, **kw)


def infos():
    return f('infos', 'Encadré « infos pratiques »', 'repeater', 'Une ligne par information, dans l’ordre d’affichage.',
             button_label='Ajouter une information', layout='table', sub_fields=[
                 f('label', 'Libellé', help='Ex. « Adresse », « Horaires ».', width=28),
                 f('value', 'Valeur', width=52),
                 f('live', 'En direct', 'true_false', 'Rond noir devant la valeur.', width=20),
             ])


def link(name, label, help=''):
    return f(name, label, 'link', help or 'Texte du bouton et lien. Pour une page du site, cherchez-la dans la liste.')


GROUPS = [
    ('front', 'Page d’accueil', [[('page_type', 'front_page')]], [
        f('bonplan_label', 'Bandeau du haut : pastille', help='Ex. « Le bon plan ». Laisser le texte vide pour masquer le bandeau.'),
        f('bonplan_text', 'Bandeau du haut : texte'),
        link('bonplan_btn', 'Bandeau du haut : bouton'),
        f('hero_title', 'Grand titre', 'textarea', 'Un retour à la ligne ici = un retour à la ligne sur le site.', rows=2),
        f('hero_lead', 'Sous-titre'),
        f('tiles_title', 'Titre au-dessus des rubriques', help='Ex. « Que voulez-vous faire ? »'),
    ]),
    ('rubrique', 'Rubrique', [[('page_template', t)] for t in TEMPLATES.values()], [
        f('h1', 'Grand titre', help='Titre affiché en tête de page. Le titre WordPress (tout en haut) sert de libellé court : menu, fil d’Ariane, tuiles.'),
        f('lead', 'Chapô', 'textarea', 'Texte d’introduction sous le grand titre.', rows=3),
        f('tile_desc', 'Texte de la tuile d’accueil', help='Ex. « 10 bonnes raisons de faire le Voyage à Nantes ».'),
        f('home', 'Afficher en tuile sur l’accueil et dans « Autres rubriques »', 'true_false'),
        f('nav', 'Afficher dans le menu principal', 'true_false'),
    ]),
    ('rubrique_lieux', 'Liste de fiches', [[('page_template', TEMPLATES['rubrique-lieux'])]], [
        f('post_type', 'Fiches à afficher', 'select', 'Les fiches de ce type s’affichent en mosaïque, dans l’ordre de leur champ « Ordre ».',
          choices=POST_TYPES),
        f('encart_title', 'Encadré de mise en avant : titre', help='Laisser vide pour ne pas afficher d’encadré.'),
        f('encart_text', 'Encadré de mise en avant : texte', 'textarea', rows=3),
        link('encart_btn', 'Encadré de mise en avant : bouton'),
    ]),
    ('liste', 'Liste d’adresses', [[('page_template', TEMPLATES['liste'])]], [
        link('cta', 'Bouton sous le chapô', 'Facultatif.'),
        f('list_title', 'Titre de la liste'),
        f('items', 'Adresses', 'repeater', 'Une ligne par adresse. Avec au moins une photo, la liste s’affiche illustrée (photo, nom, description, bouton).',
          button_label='Ajouter une adresse', layout='block', sub_fields=[
              f('title', 'Nom'),
              f('precision', 'Précision', help='Facultatif. Ex. « 10,6 km · notre coup de cœur ».'),
              f('desc', 'Description', 'textarea', rows=2),
              f('link', 'Lien', 'url', 'Adresse de la page vers laquelle mène le bouton.'),
              f('photo', 'Photo', 'image', 'Facultatif.'),
          ]),
        f('link_label', 'Texte des boutons', help='Ex. « Voir la fiche ».'),
        f('after_title', 'Encadré final : titre', help='Laisser vide pour ne pas afficher d’encadré.'),
        f('after_text', 'Encadré final : texte', 'textarea', rows=3),
        link('after_btn1', 'Encadré final : bouton principal'),
        link('after_btn2', 'Encadré final : bouton secondaire'),
    ]),
    ('infos', 'Page d’infos', [[('page_template', TEMPLATES['infos'])]], [
        infos(),
        f('maps', 'Lien du bouton « Itinéraire »', 'url', 'Facultatif : lien Google Maps.'),
    ]),
    ('rdv_quartier', 'Quartier', [[('post_type', 'rdv_quartier')]], [
        f('amb', 'Ambiance (titre de la case)', help='Ex. « Cocktails chic et cosy ».'),
        f('where', 'Localisation', help='Ex. « Graslin · autour du théâtre ».'),
        f('badges', 'Étiquettes de la case', 'textarea', 'Une étiquette par ligne. Ex. « Soirs de match ».', rows=2),
        f('lead', 'Chapô', 'textarea', rows=3),
        f('musts', 'Étapes incontournables', 'repeater', button_label='Ajouter une étape', layout='table', sub_fields=[
            f('title', 'Titre', width=30),
            f('desc', 'Description', 'textarea', rows=2, width=50),
            f('tag', 'Mention en étiquette', help='Facultatif.', width=20),
        ]),
        infos(),
        f('maps', 'Lien « Itinéraire »', 'url', 'Lien Google Maps. Laisser vide pour ne pas afficher de bouton (lieux répartis dans la ville).'),
        f('groups', 'Bars', 'repeater', 'Un groupe par titre (ex. « Nos suggestions »), avec ses bars.',
          button_label='Ajouter un groupe', layout='block', sub_fields=[
              f('title', 'Titre du groupe'),
              f('bars', 'Bars', 'repeater', button_label='Ajouter un bar', layout='table', sub_fields=[
                  f('name', 'Nom', width=24),
                  f('addr', 'Adresse', help='Sans « Nantes ».', width=26),
                  f('tag', 'Mention en étiquette', help='Facultatif.', width=18),
                  f('desc', 'Description', 'textarea', 'Facultatif.', rows=2, width=32),
              ]),
          ]),
        f('note', 'Note grise en bas de page', 'textarea', rows=2),
        f('img_alt', 'Texte si pas d’image à la une', help='Affiché à la place de la photo. Ex. « [Photo quartier Graslin] ».'),
    ]),
    ('lieu', 'Fiche', [[('post_type', t)] for t in ('rdv_incontournable', 'rdv_shopping', 'rdv_pass')], [
        f('subtitle', 'Sous-titre', help='Affiché sous le titre, et sur la case de la mosaïque.'),
        infos(),
        f('maps', 'Lien « Itinéraire »', 'url', 'Lien Google Maps.'),
        f('buy', 'Lien d’achat / réservation', 'url'),
        f('buy_label', 'Texte du bouton d’achat', help='Par défaut : « Acheter en ligne ».'),
        f('more', 'Lien « En savoir plus »', 'url'),
        link('link', 'Bouton vers une autre page', 'Facultatif. Ex. « Prendre un verre au Bouffay ».'),
    ]),
]

COPY_TYPES = {'true_false', 'select', 'image'}


def build(field, prefix, parent):
    """Champ ACF complet : clé stable, nom préfixé « rdv_ » pour les champs de premier niveau."""
    key = prefix + '_' + field['name']
    out = {
        'key': key,
        'label': field['label'],
        'name': ('rdv_' if parent.startswith('group_') else '') + field['name'],
        'aria-label': '',
        'type': field['type'],
        'instructions': field['instructions'],
        'required': 0,
        'conditional_logic': 0,
        'wrapper': {'width': str(field.get('width', '')), 'class': '', 'id': ''},
        'wpml_cf_preferences': COPY if field['type'] in COPY_TYPES else TRANSLATE,
    }
    t = field['type']
    if t in ('text', 'url'):
        out.update(default_value='', placeholder='')
        if t == 'text':
            out.update(maxlength='', prepend='', append='')
    elif t == 'textarea':
        out.update(default_value='', placeholder='', maxlength='', rows=field.get('rows', 4), new_lines='')
    elif t == 'true_false':
        out.update(message='', default_value=0, ui=1, ui_on_text='', ui_off_text='')
    elif t == 'select':
        out.update(choices=field['choices'], default_value=False, return_format='value', multiple=0, allow_null=1,
                   ui=0, ajax=0, placeholder='')
    elif t == 'link':
        out.update(return_format='array')
    elif t == 'image':
        out.update(return_format='id', library='all', preview_size='medium', min_width='', min_height='', min_size='',
                   max_width='', max_height='', max_size='', mime_types='')
    elif t == 'repeater':
        subs = [build(s, key, key) for s in field['sub_fields']]
        for s in subs:
            s['parent_repeater'] = key
        out.update(layout=field['layout'], pagination=0, min=0, max=0, collapsed='', button_label=field['button_label'],
                   rows_per_page=20, sub_fields=subs)
    return out


def main():
    os.makedirs(OUT, exist_ok=True)
    for order, (gid, title, locations, fields) in enumerate(GROUPS):
        gkey = 'group_rdv_' + gid
        group = {
            'key': gkey,
            'title': title,
            'fields': [build(fd, 'field_rdv_' + gid, gkey) for fd in fields],
            'location': [[{'param': p, 'operator': '==', 'value': v} for p, v in rule] for rule in locations],
            'menu_order': order,
            'position': 'normal',
            'style': 'default',
            'label_placement': 'top',
            'instruction_placement': 'label',
            'hide_on_screen': '',
            'active': True,
            'description': 'Thème Rendez-vous à Nantes.',
            'show_in_rest': 0,
            'acfml_field_group_mode': 'advanced',
            'modified': 0,
        }
        # Date de modification : celle du fichier existant s'il est identique (sinon ACF proposerait
        # une « synchronisation » inutile), l'heure actuelle s'il a changé.
        path = os.path.join(OUT, gkey + '.json')
        old = json.load(open(path, encoding='utf-8')) if os.path.exists(path) else None
        if old and {**old, 'modified': 0} == group:
            group['modified'] = old['modified']
        else:
            group['modified'] = int(time.time())
        with open(path, 'w', encoding='utf-8') as fh:
            json.dump(group, fh, ensure_ascii=False, indent=4)
            fh.write('\n')
    print(len(GROUPS), 'groupes écrits dans', OUT)
    write_wpml_config()


def write_wpml_config():
    """Met à jour la liste des champs de wpml-config.xml (champs de premier niveau ; ACF Multilingual gère le détail)."""
    path = os.path.join(ROOT, 'rdv-nantes', 'wpml-config.xml')
    xml = open(path, encoding='utf-8').read()
    prefs = {}
    for gid, title, locations, fields in GROUPS:
        for fd in fields:
            prefs.setdefault('rdv_' + fd['name'], 'copy' if fd['type'] in COPY_TYPES else 'translate')
    lines = ['  <custom-fields>', '    <!-- Généré par tools/build_acf.py : textes à traduire, réglages copiés depuis le français -->']
    lines += ['    <custom-field action="%s">%s</custom-field>' % (a, n) for n, a in sorted(prefs.items(), key=lambda x: (x[1] != 'translate', x[0]))]
    lines += ['    <custom-field action="ignore">_rdv_import_key</custom-field>', '  </custom-fields>']
    start, end = xml.index('  <custom-fields>'), xml.index('  </custom-fields>') + len('  </custom-fields>')
    with open(path, 'w', encoding='utf-8') as fh:
        fh.write(xml[:start] + '\n'.join(lines) + xml[end:])


if __name__ == '__main__':
    main()
