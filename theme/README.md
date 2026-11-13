# Thème WordPress « Rendez-vous à Nantes »

Thème de la refonte de https://rdv.levoyageanantes.fr/, reprenant **telle quelle la maquette V1** ([`maquette/rdv-nantes-v1.html`](../maquette/rdv-nantes-v1.html)) : mêmes styles, mêmes gabarits, même contenu FR. Arborescence calée sur le sitemap du site actuel (sans les pages événementielles : JO 2024 / Rugby 2023, Marathon, Squash, Rendez-vous en France, page japonaise).

- WordPress ≥ 6.2, PHP ≥ 7.4
- **ACF PRO** (Advanced Custom Fields) pour modifier les champs dans l'admin ; le site s'affiche aussi sans (démo, ACF désactivé). **WPML** + **ACF Multilingual** pour EN / ES.

## Démo en ligne

[Ouvrir dans WordPress Playground](https://playground.wordpress.net/?mode=seamless&blueprint-url=https://raw.githubusercontent.com/matmaaar/rendez-vous-a-nantes/claude/elegant-ride-bswwdv/blueprint.json) : WordPress jetable dans le navigateur, thème installé et contenu FR importé (WPML n'étant pas disponible sur Playground, seul le français y est visible). Configuration : [`blueprint.json`](../blueprint.json). Le blueprint installe `theme/rdv-nantes.zip` : le régénérer après chaque modification du thème.

> **Mise en ligne sur le site actuel** : suivre [`DEPLOIEMENT.md`](DEPLOIEMENT.md) (sauvegarde, simulation d'import, réglages WPML, redirections, recette, retour arrière).

## Installation

1. Copier le dossier `rdv-nantes/` dans `wp-content/themes/` (ou téléverser `rdv-nantes.zip` via Apparence → Thèmes → Ajouter).
2. Si multilingue : installer **WPML** (+ String Translation), langues **FR (par défaut), EN, ES**, format d'URL « répertoire » (`/en/`, `/es/`).
3. Activer le thème, puis **ACF → Groupes de champs → « Synchronisation disponible » → Synchroniser** les 7 groupes du thème (une seule fois : ils deviennent modifiables dans l'admin).
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

Les restaurants de la page Manger s'affichent en **liste illustrée** (photo, nom, description, bouton vers la fiche du guide Les Tables de Nantes), sur 2 colonnes. La photo est la case « Photo » de chaque adresse ; une liste sans photo (Footing) reste en lignes simples. Photos reprises des fiches du guide (voir `contenu/images/tables-de-nantes/`). Maison Bagarre a été retiré : sa fiche n'existe plus sur le guide.

## Édition du contenu

Tout se modifie dans l'admin, dans les encadrés **ACF** sous l'éditeur :

- **Pages de rubrique** : choisir le gabarit, enregistrer, puis remplir « Rubrique » (grand titre, chapô, texte de tuile, cases « accueil » et « menu ») et l'encadré propre au gabarit. Le **titre de la page** est le libellé court (menu, fil d'Ariane, tuiles). L'ordre des rubriques = champ **Ordre** de la page.
- **Accueil** : bandeau du haut (Pass Nantes), grand titre et sous-titre. La mosaïque reprend automatiquement les rubriques.
- **Fiches** : sous-titre, infos pratiques, liens Itinéraire / En savoir plus / achat. Ordre = champ **Ordre**.
- **Quartiers** : ambiance, localisation, lien Itinéraire (facultatif), étiquettes, bars, image à la une (vignette).

Les listes (infos pratiques, adresses de Manger et Footing, étapes et bars des quartiers) ont une ligne par élément, avec un bouton « Ajouter ». Les boutons se règlent avec un champ « Lien » (texte + adresse) : pour une page du site, la choisir dans la liste ou écrire son **chemin français** (`/incontournable/quartier-bouffay/`) ; le thème convertit le lien vers la langue affichée.

### Champs ACF

- Définis dans `rdv-nantes/acf-json/` (« Local JSON » d'ACF), 7 groupes : Page d'accueil, Rubrique, Liste de fiches, Liste d'adresses, Page d'infos, Quartier, Fiche. Noms des champs préfixés `rdv_`.
- **Ajouter ou modifier une case** : ACF → Groupes de champs → le groupe → enregistrer. ACF réécrit le fichier du groupe dans `acf-json/` (le dossier doit être inscriptible) : récupérer ce fichier pour le garder dans le dépôt. L'afficher sur le site demande ensuite de modifier le gabarit correspondant (lecture : `rdv_meta()`, `rdv_field()`, `rdv_rows()` dans `inc/fields.php`).
- `tools/build_acf.py` génère ces fichiers et la liste des champs de `wpml-config.xml`. Une fois des cases ajoutées dans l'admin, **reporter la modification dans le script** avant de le relancer, sinon il écrase les fichiers.
- L'affichage lit directement les valeurs enregistrées par ACF (méta `rdv_<nom>`, lignes `rdv_<liste>_<n>_<sous-champ>`) : le site reste identique si ACF est désactivé.
- Traductions (ACF Multilingual, mode « Expert ») : textes et liens traduits, réglages (cases à cocher, type de fiches, photos) copiés depuis le français.
- Groupes ACF de l'ancien thème : notés à l'activation du thème et **masqués** sur les écrans des pages et fiches du thème (option `rdv_legacy_acf_groups`). Ils ne sont ni modifiés ni supprimés (retour arrière possible) ; les groupes créés ensuite restent visibles.

## Traductions

- **Contenus** : FR = textes de la maquette ; EN / ES = **textes du site actuel** partout où ils existent (fiches, quartiers, chapôs, encadrés, listes, noms de rubriques, coquilles évidentes du site corrigées). Seuls les textes **créés par la maquette**, sans équivalent sur le site actuel (accroche et parcours de l'accueil, textes des tuiles, ambiances et localisations des quartiers…), ont été traduits : liste à faire relire dans [`TRADUCTIONS-A-RELIRE.md`](TRADUCTIONS-A-RELIRE.md).
- **SEO** : titre et meta description de l'accueil (FR/EN/ES) et `noindex` des mentions légales repris du site actuel (champs Yoast). Sans Yoast, le thème affiche quand même la meta description et le `noindex`.
- **Crédits photo** : les images à la une importées portent leur texte alternatif et leur crédit en légende (export WordPress du site actuel). Graslin n'a pas de crédit connu ; celui de l'Île de Nantes est déduit du nom de fichier : à vérifier.
- **Interface** (pied de page, boutons…) : textes écrits en français dans le thème ; `languages/en_US.mo` et `es_ES.mo` (sources `.po`, modèle `.pot`) ne sont chargés que pour les pages EN / ES de WPML. Un WordPress réglé en anglais affiche donc bien la version FR en français. Modifiables aussi via WPML String Translation.

## Points à valider

- Mentions éditoriales provisoires : dates des guinguettes (fiche Île de Nantes) et horaires d'été de l'Accueil (page Aide), marquées « à mettre à jour ».
- La maquette prévoyait une description courte par bar (« [Description courte à rédiger…] ») : la case « Description » de chaque bar existe mais est vide, rien ne s'affiche tant qu'elle n'est pas remplie.
- Les vignettes de quartiers utilisent une photo du site actuel (image à la une) au lieu des cases « [Photo …] » de la maquette.
- Lien « Accessibilité » du pied de page : affiché dès qu'une page de slug `accessibilite` existe (aucun contenu fourni).
- Polices : **Suisse Int'l** (licence web LVaN) est chargée depuis `rdv-nantes/assets/fonts/` si les fichiers WOFF2 y sont déposés (voir `LISEZMOI.md` du dossier) ; ils ne sont **pas versionnés** car le repo est public. Sans eux, le thème affiche Inter (Google Fonts).

## Développement

- `tools/extract_maquette.py` : maquette HTML → `tools/maquette.json`.
- `tools/build_content.py` : `maquette.json` + `../contenu/` → `rdv-nantes/import/content.json` et images des quartiers. Le contenu reste au format texte (« Libellé | lien », une ligne par élément) ; l'import le convertit en champs ACF (`inc/import-fields.php`).
- `tools/build_acf.py` : champs ACF (`rdv-nantes/acf-json/`) et liste des champs de `wpml-config.xml`.
- `../contenu/outils/wxr_extract.py <export.xml> <acf.json>` : exploite l'export WordPress / ACF du site actuel (crédits, SEO, redirections, contenus non publiés). Les exports bruts ne sont pas versionnés.
- Zip d'installation (polices exclues, le repo étant public) : `cd theme && rm -f rdv-nantes.zip && zip -qr rdv-nantes.zip rdv-nantes -x 'rdv-nantes/assets/fonts/*'`.

## Tests

Dernière recette (novembre 2026, thème v1.2.0) sur les versions du site actuel : **WordPress 7.1.4, PHP 7.4.33**, **ACF PRO 6.3.1, WPML 4.9.7, WPML String Translation 3.5.4, ACF Multilingual 2.2.4** (extensions reprises de la sauvegarde du site, jamais versionnées).

- WPML configuré (FR par défaut, EN, ES, adresses en répertoires), import : 32 contenus EN et 32 ES créés et reliés aux 33 FR. Import rejoué : aucun doublon.
- 97 pages (FR, EN, ES) à 1280 px et 390 px : aucune erreur PHP ni JavaScript, aucun lien interne (98) ni image cassés, aucun débordement horizontal sur mobile, un seul `h1` par page.
- Admin : les 7 groupes ACF s'affichent sur les bons écrans, remplis par l'import ; modification d'une liste et enregistrement vérifiés (la traduction n'est pas touchée) ; synchronisation des groupes vérifiée ; groupes de l'ancien thème masqués.
- Sans ACF (et sans WPML, comme sur Playground) : HTML des pages identique à celui avec ACF ; rendu identique à la version précédente du thème.
- Interface du thème : FR quel que soit le réglage de langue de WordPress, EN / ES chargés seulement sur les pages WPML de ces langues.
- Analyse de compatibilité du code (PHPCompatibility) : rien d'incompatible de PHP 7.4 à 8.5.
- Non testé : les slugs traduits des fiches EN / ES (réglage WPML, voir plus haut), et le site réel avec ses anciens contenus : à vérifier sur une copie (WP Staging), voir [`DEPLOIEMENT.md`](DEPLOIEMENT.md).
