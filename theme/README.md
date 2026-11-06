# Thème WordPress « Rendez-vous à Nantes »

Thème de la refonte de https://rdv.levoyageanantes.fr/, reprenant **telle quelle la maquette V1** ([`maquette/rdv-nantes-v1.html`](../maquette/rdv-nantes-v1.html)) : mêmes styles, mêmes gabarits, même contenu FR. Arborescence calée sur le sitemap du site actuel (sans les pages événementielles : JO 2024 / Rugby 2023, Marathon, Squash, Rendez-vous en France, page japonaise).

- WordPress ≥ 6.2, PHP ≥ 7.4
- **Aucun plugin requis** (champs personnalisés natifs). **WPML** recommandé pour EN / ES.

## Démo en ligne

[Ouvrir dans WordPress Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/matmaaar/rendez-vous-a-nantes/claude/elegant-ride-bswwdv/blueprint.json) : WordPress jetable dans le navigateur, thème installé et contenu FR importé (WPML n'étant pas disponible sur Playground, seul le français y est visible). Configuration : [`blueprint.json`](../blueprint.json). Le blueprint installe `theme/rdv-nantes.zip` : le régénérer après chaque modification du thème.

## Installation

1. Copier le dossier `rdv-nantes/` dans `wp-content/themes/` (ou téléverser `rdv-nantes.zip` via Apparence → Thèmes → Ajouter).
2. Si multilingue : installer **WPML** (+ String Translation), langues **FR (par défaut), EN, ES**, format d'URL « répertoire » (`/en/`, `/es/`).
3. Activer le thème.
4. Importer le contenu : **Outils → Import Rendez-vous à Nantes**, ou en ligne de commande :
   ```bash
   wp rdv import
   ```
   L'import crée les pages, rubriques et fiches, les relie entre langues sous WPML, définit la page d'accueil et les permaliens `/%postname%/`. Il est **rejouable** (met à jour au lieu de dupliquer). Sans WPML, seul le FR est importé.

## Arborescence (URL du site actuel conservées)

| Rubrique | Gabarit | FR | EN | ES |
| --- | --- | --- | --- | --- |
| Accueil | `front-page.php` | `/` | `/en/` | `/es/` |
| 01 Prendre un verre (carte + 4 quartiers) | Rubrique — quartiers avec carte | `/prendre-un-verre/` | `/en/have-a-drink/` | `/es/tomar-una-copa/` |
| 02 Manger | Rubrique — liste d'adresses | `/manger/` | `/en/eat/` | `/es/comer/` |
| 03 Incontournables (10 fiches) | Rubrique — liste de fiches | `/incontournable/` | `/en/unmissable/` | `/es/imprescindibles/` |
| 04 Footing | Rubrique — liste d'adresses | `/footing/` | `/en/jogging/` | `/es/footing/` |
| 05 Shopping (5 fiches) | Rubrique — liste de fiches | `/shopping/` | `/en/shopping/` | `/es/shopping/` |
| 06 Se déplacer | Rubrique — page d'infos | `/access/` | `/en/getting-around/` | `/es/desplazarse/` |
| 07 Aide | Rubrique — page d'infos | `/aide/` | `/en/need-a-hand/` | `/es/ayuda/` |
| 08 Pass Nantes (3 fiches) | Rubrique — liste de fiches | `/24h-de-plus/` | `/en/24h-more/` | `/es/24h-mas/` |
| Mentions légales, cookies | `page.php` | `/mentions-legales/`, `/cookiespolicy/` | `/en/legal-information/` | `/es/mentions-legales/` |

Fiches : types de contenu `rdv_quartier` (`/prendre-un-verre/…`), `rdv_incontournable` (`/incontournable/…`), `rdv_shopping` (`/shopping/…`), `rdv_pass` (`/24h-de-plus/…`). Une rubrique est une page dont le slug est la base d'URL de ses fiches.

**À régler dans WPML** pour retrouver exactement les anciennes URL de fiches en EN/ES (sinon elles fonctionnent en `/en/incontournable/…`) : WPML → Paramètres → Types de contenu → « Traduire les slugs » : `incontournable` → `unmissable` / `imprescindibles`, `prendre-un-verre` → `have-a-drink` / `tomar-una-copa`, `24h-de-plus` → `24h-more` / `24h-mas`.

Slugs corrigés par rapport au site actuel : `/shopping/uartier-decre/` → `/shopping/quartier-decre/` (son slug d'origine), `/en/unmissable/1279/` → `…/the-lieu-unique/`, `/es/shopping/mercato-detalensac/` → `…/mercado-de-talensac/`.

**Redirections 301** : [`redirections.csv`](redirections.csv) (29 règles, à importer dans le plugin *Redirection* : Outils → Redirection → Import/Export). Elles couvrent les anciens slugs mémorisés par WordPress, les slugs corrigés, les pages événementielles et JO 2024 non reprises (→ accueil de la langue) et le lien cassé `/24h-mas/`. Le site actuel n'en avait aucune.

## Édition du contenu

Tout se modifie dans l'admin, dans les encadrés sous l'éditeur :

- **Pages de rubrique** : choisir le gabarit, enregistrer, puis remplir « Rubrique » (numéro, grand titre, chapô, texte de tuile, cases « accueil » et « menu ») et l'encadré propre au gabarit. Le **titre de la page** est le libellé court (menu, fil d'Ariane, tuiles). L'ordre des rubriques = champ **Ordre** de la page.
- **Accueil** : bandeau jaune, grand titre, parcours « Visiter Nantes à pied ».
- **Fiches** : sous-titre, infos pratiques, liens Itinéraire / En savoir plus / achat. Ordre = champ **Ordre**.
- **Quartiers** : ambiance, localisation, position sur la carte (en %), étiquettes, bars, image à la une (vignette).

Conventions des champs multi-lignes : **une ligne = un élément**, colonnes séparées par `|`, par ex. infos pratiques `Adresse | 4 place Marc Elder | 1` (le `1` final affiche la pastille verte). Les liens internes s'écrivent en **chemin français** (`/incontournable/quartier-bouffay/`) : le thème les convertit vers la bonne langue.

## Traductions

- **Contenus** : FR = textes de la maquette ; EN / ES = textes du site actuel, transposés dans la structure de la maquette. Les textes courts propres à la maquette (accroches des tuiles, ambiances, étiquettes, encadrés, parcours de l'accueil) ont été traduits pour l'occasion : **à faire relire**.
- **SEO** : titre et meta description de l'accueil (FR/EN/ES) et `noindex` des mentions légales repris du site actuel (champs Yoast). Sans Yoast, le thème affiche quand même la meta description et le `noindex`.
- **Crédits photo** : les images à la une importées portent leur texte alternatif et leur crédit en légende (export WordPress du site actuel). Graslin n'a pas de crédit connu ; celui de l'Île de Nantes est déduit du nom de fichier : à vérifier.
- **Interface** (pied de page, boutons, carte…) : `languages/en_US.mo` et `es_ES.mo` (sources `.po`, modèle `.pot`). Modifiables aussi via WPML String Translation.

## Points à valider

- Mentions éditoriales de la maquette reprises telles quelles : « [X] min à pied de la Cité des Congrès », « Dates 2024 à mettre à jour », « 2025 à mettre à jour ».
- La maquette prévoyait une description courte par bar (« [Description courte à rédiger…] ») : la colonne existe (4e colonne du champ Bars) mais est vide, rien ne s'affiche tant qu'elle n'est pas remplie.
- Les vignettes de quartiers utilisent une photo du site actuel (image à la une) au lieu des cases « [Photo …] » de la maquette.
- Lien « Accessibilité » du pied de page : affiché dès qu'une page de slug `accessibilite` existe (aucun contenu fourni).
- Polices : la maquette appelle « Suisse Int'l » (non fournie) puis Inter (Google Fonts) ; le thème fait de même.

## Développement

- `tools/extract_maquette.py` : maquette HTML → `tools/maquette.json` + carte `assets/img/plan-nantes.webp`.
- `tools/build_content.py` : `maquette.json` + `../contenu/` → `rdv-nantes/import/content.json` et images des quartiers.
- `../contenu/outils/wxr_extract.py <export.xml> <acf.json>` : exploite l'export WordPress / ACF du site actuel (crédits, SEO, redirections, contenus non publiés). Les exports bruts ne sont pas versionnés.
- Zip d'installation : `cd theme && zip -r rdv-nantes.zip rdv-nantes`.

Testé sur WordPress 6.4 (sans WPML) : toutes les pages répondent, rendu identique à la maquette à 1280 px et 390 px (écart < 0,5 % des pixels, hors photos des quartiers), import rejouable, enregistrement des champs dans l'admin.
