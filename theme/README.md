# Thème WordPress « Rendez-vous à Nantes »

Thème de la refonte de https://rdv.levoyageanantes.fr/, reprenant **telle quelle la maquette V1** ([`maquette/rdv-nantes-v1.html`](../maquette/rdv-nantes-v1.html)) : mêmes styles, mêmes gabarits, même contenu FR. Arborescence calée sur le sitemap du site actuel (sans les pages événementielles : JO 2024 / Rugby 2023, Marathon, Squash, Rendez-vous en France, page japonaise).

- WordPress ≥ 6.2, PHP ≥ 7.4
- **Aucun plugin requis** (champs personnalisés natifs). **WPML** recommandé pour EN / ES.

## Démo en ligne

[Ouvrir dans WordPress Playground](https://playground.wordpress.net/?mode=seamless&blueprint-url=https://raw.githubusercontent.com/matmaaar/rendez-vous-a-nantes/claude/elegant-ride-bswwdv/blueprint.json) : WordPress jetable dans le navigateur, thème installé et contenu FR importé (WPML n'étant pas disponible sur Playground, seul le français y est visible). Configuration : [`blueprint.json`](../blueprint.json). Le blueprint installe `theme/rdv-nantes.zip` : le régénérer après chaque modification du thème.

> **Mise en ligne sur le site actuel** : suivre [`DEPLOIEMENT.md`](DEPLOIEMENT.md) (sauvegarde, simulation d'import, réglages WPML, redirections, recette, retour arrière).

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
| Prendre un verre (4 quartiers) | Rubrique — quartiers | `/prendre-un-verre/` | `/en/have-a-drink/` | `/es/tomar-una-copa/` |
| Manger | Rubrique — liste d'adresses | `/manger/` | `/en/eat/` | `/es/comer/` |
| Incontournables (10 fiches) | Rubrique — liste de fiches | `/incontournable/` | `/en/unmissable/` | `/es/imprescindibles/` |
| Footing | Rubrique — liste d'adresses | `/footing/` | `/en/jogging/` | `/es/footing/` |
| Shopping (5 fiches) | Rubrique — liste de fiches | `/shopping/` | `/en/shopping/` | `/es/shopping/` |
| Se déplacer | Rubrique — page d'infos | `/access/` | `/en/getting-around/` | `/es/desplazarse/` |
| Aide | Rubrique — page d'infos | `/aide/` | `/en/need-a-hand/` | `/es/ayuda/` |
| Pass Nantes (3 fiches) | Rubrique — liste de fiches | `/24h-de-plus/` | `/en/24h-more/` | `/es/24h-mas/` |
| Mentions légales, cookies | `page.php` | `/mentions-legales/`, `/cookiespolicy/` | `/en/legal-information/` | `/es/mentions-legales/` |

Fiches : types de contenu `rdv_quartier` (`/prendre-un-verre/…`), `rdv_incontournable` (`/incontournable/…`), `rdv_shopping` (`/shopping/…`), `rdv_pass` (`/24h-de-plus/…`). Une rubrique est une page dont le slug est la base d'URL de ses fiches.

**À régler dans WPML** pour retrouver exactement les anciennes URL de fiches en EN/ES (sinon elles fonctionnent en `/en/incontournable/…`) : WPML → Paramètres → Types de contenu → « Traduire les slugs » : `incontournable` → `unmissable` / `imprescindibles`, `prendre-un-verre` → `have-a-drink` / `tomar-una-copa`, `24h-de-plus` → `24h-more` / `24h-mas`.

Slugs corrigés par rapport au site actuel : `/shopping/uartier-decre/` → `/shopping/quartier-decre/` (son slug d'origine), `/en/unmissable/1279/` → `…/the-lieu-unique/`, `/es/shopping/mercato-detalensac/` → `…/mercado-de-talensac/`.

**Redirections 301** : [`redirections.csv`](redirections.csv) (29 règles, à importer dans le plugin *Redirection* : Outils → Redirection → Import/Export). Elles couvrent les anciens slugs mémorisés par WordPress, les slugs corrigés, les pages événementielles et JO 2024 non reprises (→ accueil de la langue) et le lien cassé `/24h-mas/`. Le site actuel n'en avait aucune.

## Choix de mise en page par rapport à la maquette

- Aucun surtitre au-dessus des titres (rubriques, pages d'infos, fiches) : il répétait le titre ou le fil d'Ariane.
- Pas de carte sur Prendre un verre : les quartiers sont présentés en mosaïque photo.
- Menu mobile et tablette (≤ 1180 px) : plus de bandeau de rubriques en haut ; une pastille « ☰ Menu » flotte en bas à droite et fait monter un panneau avec les rubriques et Pass Nantes (`footer.php`, `assets/js/menu.js`). Le menu horizontal reste sur ordinateur.
- Couleurs : plus de couleur d'accent (jaune, vert, vert fluo). Un seul fond de mise en avant, `--accent: #e6e6f2` dans `style.css` (bandeau Pass Nantes, encadrés, étiquettes, pied de page), à ajuster à un seul endroit.
- Pas de parcours « Visiter Nantes à pied » sur l'accueil : bandeau Pass Nantes, titre, puis la mosaïque des rubriques.
- Favicon : pictogramme repère de la maquette (`assets/img/`). Une icône définie dans Personnaliser → Identité du site le remplace.
- Survols sobres : soulignement noir, léger zoom des photos, gris. Pas de couleur vive.
- Performance : pas de styles de l'éditeur de blocs ni d'emojis WordPress, images en WebP (si le serveur sait les produire) avec une taille dédiée aux téléphones, 1re photo des mosaïques chargée en priorité. Audit Lighthouse mobile (serveur local) : accessibilité 100, bonnes pratiques 100, SEO 100, performance 90 à 99.

## Mosaïque photo

Les grilles de cases (accueil, Prendre un verre, Incontournables, Shopping, Pass Nantes) sont des **mosaïques photo** : photo pleine case en 3/2, cases collées, titre et accroche en blanc sur un halo sombre très diffus, sans numéro ; au survol, léger zoom de la photo et titre souligné. 4 colonnes à partir de 8 cases (et pour les 4 quartiers), sinon 3 ; 2 colonnes sur tablette, 1 sur mobile. Fonction : `rdv_mosaic()` dans `inc/template-tags.php`.

La photo d'une case est l'**image à la une** de la fiche ou de la page de rubrique ; la même photo s'affiche en tête de la fiche, avec sa légende (crédit) (sans image : fond gris foncé). L'import en pose une pour chaque fiche et rubrique, reprise du diaporama du site actuel, avec le crédit en légende.

## Page Manger : liste illustrée

Les restaurants de la page Manger s'affichent en **liste illustrée** (photo, nom, description, bouton vers la fiche du guide Les Tables de Nantes), sur 2 colonnes. La photo est la 5e colonne du champ « Éléments » (numéro du média) ; une liste sans photo (Footing) reste en lignes simples. Photos reprises des fiches du guide (voir `contenu/images/tables-de-nantes/`). Maison Bagarre a été retiré : sa fiche n'existe plus sur le guide.

## Édition du contenu

Tout se modifie dans l'admin, dans les encadrés sous l'éditeur :

- **Pages de rubrique** : choisir le gabarit, enregistrer, puis remplir « Rubrique » (grand titre, chapô, texte de tuile, cases « accueil » et « menu ») et l'encadré propre au gabarit. Le **titre de la page** est le libellé court (menu, fil d'Ariane, tuiles). L'ordre des rubriques = champ **Ordre** de la page.
- **Accueil** : bandeau du haut (Pass Nantes), grand titre et sous-titre. La mosaïque reprend automatiquement les rubriques.
- **Fiches** : sous-titre, infos pratiques, liens Itinéraire / En savoir plus / achat. Ordre = champ **Ordre**.
- **Quartiers** : ambiance, localisation, lien Itinéraire (facultatif), étiquettes, bars, image à la une (vignette).

Conventions des champs multi-lignes : **une ligne = un élément**, colonnes séparées par `|`, par ex. infos pratiques `Adresse | 4 place Marc Elder | 1` (le `1` final affiche une pastille « en direct »). Les liens internes s'écrivent en **chemin français** (`/incontournable/quartier-bouffay/`) : le thème les convertit vers la bonne langue.

## Traductions

- **Contenus** : FR = textes de la maquette ; EN / ES = **textes du site actuel** partout où ils existent (fiches, quartiers, chapôs, encadrés, listes, noms de rubriques, coquilles évidentes du site corrigées). Seuls les textes **créés par la maquette**, sans équivalent sur le site actuel (accroche et parcours de l'accueil, textes des tuiles, ambiances et localisations des quartiers…), ont été traduits : liste à faire relire dans [`TRADUCTIONS-A-RELIRE.md`](TRADUCTIONS-A-RELIRE.md).
- **SEO** : titre et meta description de l'accueil (FR/EN/ES) et `noindex` des mentions légales repris du site actuel (champs Yoast). Sans Yoast, le thème affiche quand même la meta description et le `noindex`.
- **Crédits photo** : les images à la une importées portent leur texte alternatif et leur crédit en légende (export WordPress du site actuel). Graslin n'a pas de crédit connu ; celui de l'Île de Nantes est déduit du nom de fichier : à vérifier.
- **Interface** (pied de page, boutons…) : textes écrits en français dans le thème ; `languages/en_US.mo` et `es_ES.mo` (sources `.po`, modèle `.pot`) ne sont chargés que pour les pages EN / ES de WPML. Un WordPress réglé en anglais affiche donc bien la version FR en français. Modifiables aussi via WPML String Translation.

## Points à valider

- Mentions éditoriales provisoires : dates des guinguettes (fiche Île de Nantes) et horaires d'été de l'Accueil (page Aide), marquées « à mettre à jour ».
- La maquette prévoyait une description courte par bar (« [Description courte à rédiger…] ») : la colonne existe (4e colonne du champ Bars) mais est vide, rien ne s'affiche tant qu'elle n'est pas remplie.
- Les vignettes de quartiers utilisent une photo du site actuel (image à la une) au lieu des cases « [Photo …] » de la maquette.
- Lien « Accessibilité » du pied de page : affiché dès qu'une page de slug `accessibilite` existe (aucun contenu fourni).
- Polices : **Suisse Int'l** (licence web LVaN) est chargée depuis `rdv-nantes/assets/fonts/` si les fichiers WOFF2 y sont déposés (voir `LISEZMOI.md` du dossier) ; ils ne sont **pas versionnés** car le repo est public. Sans eux, le thème affiche Inter (Google Fonts).

## Développement

- `tools/extract_maquette.py` : maquette HTML → `tools/maquette.json`.
- `tools/build_content.py` : `maquette.json` + `../contenu/` → `rdv-nantes/import/content.json` et images des quartiers.
- `../contenu/outils/wxr_extract.py <export.xml> <acf.json>` : exploite l'export WordPress / ACF du site actuel (crédits, SEO, redirections, contenus non publiés). Les exports bruts ne sont pas versionnés.
- Zip d'installation (polices exclues, le repo étant public) : `cd theme && rm -f rdv-nantes.zip && zip -qr rdv-nantes.zip rdv-nantes -x 'rdv-nantes/assets/fonts/*'`.

## Tests

Dernière recette (novembre 2026) : **WordPress 7.1, PHP 8.3**, sans WPML (EN / ES simulés), contenu importé.

- 34 pages FR contrôlées à 1280 px et 390 px : aucune erreur PHP ni JavaScript, aucun lien interne ni image cassés, aucun débordement horizontal sur mobile, un seul `h1` par page.
- Import rejouable (aucun doublon), enregistrement des champs dans l'admin (jeton de sécurité et droits vérifiés).
- Interface du thème : FR quel que soit le réglage de langue de WordPress, EN / ES chargés seulement sur les pages WPML de ces langues.
- **Pas encore testé avec WPML** (extension payante) : à faire sur une copie du site, voir [`DEPLOIEMENT.md`](DEPLOIEMENT.md).
