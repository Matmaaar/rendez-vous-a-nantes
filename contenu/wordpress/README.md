# Export WordPress du site actuel

Synthèse de l'export WordPress (WXR, 06/11/2026) et de l'export des champs ACF fournis par l'équipe.
Les fichiers bruts ne sont **pas** versionnés (le repo est public et l'export contient les comptes utilisateurs).
Régénération : `python3 -I contenu/outils/wxr_extract.py <export.xml> <acf-export.json>`.

## Inventaire

| Type | Statut | Nombre |
| --- | --- | --- |
| 24h | draft | 6 |
| 24h | publish | 9 |
| acf-field | publish | 111 |
| acf-field-group | acf-disabled | 7 |
| acf-field-group | publish | 16 |
| attachment | inherit | 795 |
| incontournable | publish | 30 |
| lieu_verre | pending | 22 |
| lieu_verre | publish | 12 |
| page | publish | 25 |
| rugby2023 | publish | 18 |
| shopping | publish | 15 |

## Contenus non publiés

Absents du site public, donc absents du crawl : ils sont dans [`non-publies/`](non-publies/).

- 24h / draft / en : 2
- 24h / draft / es : 2
- 24h / draft / fr : 2
- lieu_verre / pending / en : 11
- lieu_verre / pending / fr : 11

- **22 fiches « Prendre un verre » en attente (2019)** : ancienne version de la rubrique, une fiche par lieu, classées « Santé » / « Pour les couche-tard » (Lieu Unique, L'Absence, Hangar à Bananes, Cantine du Voyage, Bateau Lavoir, Warehouse, Floride, L'Éléphant, Conclav, Petit Marais). Remplacées par les 4 fiches quartiers.
- **6 brouillons Pass Nantes / 24h de + (2023)** : « Le Voyage à Nantes, l'événement estival » et « Visites privées réservables en ligne » (ES jamais traduit).

Aucun de ces contenus n'est importé dans le nouveau thème.

## Champs ACF du site actuel → nouveau thème

| Groupe ACF (actif) | Champs | Équivalent dans le thème |
| --- | --- | --- |
| Fiche 24h de + | `24h_description`, `24h_more`, `24h_photos_mobile`, `24h_photos_desktop` | Fiche : contenu, En savoir plus, bouton d'achat |
| Fiche Shopping | `shopping_baseline`, `shopping_description`, `shopping_adresse`, `shopping_les_petits_plus`, `shopping_petit_plus`, `shopping_itineraire`, `shopping_more`, `shopping_photos_mobile`, `shopping_photos_desktop` | Fiche : sous-titre, contenu, infos pratiques, Itinéraire, En savoir plus |
| Home & landings | `image_de_fond`, `image_de_fond_mobile`, `image_de_fond_desktop`, `opacite_fond_home`, `afficher_logo_home`, `afficher_splashscreen_texte_personnalise`, `splashscreen_texte_personnalise` | Accueil : champs « Page d'accueil » (pas d'image de fond dans la maquette) |
| Incontournables & Rugby/JO | `inc_baseline`, `inc_description`, `inc_adresse`, `inc_les_petits_plus`, `inc_petit_plus`, `inc_tarif_plein`, `inc_tarif_reduit`, `inc_detail_tarif`, `inc_itineraire`, `inc_more`, `inc_photos_mobile`, `inc_photos_desktop` | Fiche : sous-titre, contenu, infos pratiques (adresse, tarifs, petits plus), Itinéraire, En savoir plus |
| Manger - 3 coups de pouce | `manger_coups_de_pouce`, `manger_restaurant`, `manger_restaurant_nom`, `manger_restaurant_url`, `manger_restaurant_description` | Page « liste d'adresses » : éléments (Titre | précision | description | lien) |
| Options - 24h de + | `option_24h_texte_intro`, `option_24h_photos_mobile`, `option_24h_photos_desktop` | Page de rubrique : chapô / contenu |
| Options - Home & landings | `home_video`, `contenu_home` | Accueil (la vidéo YouTube n'est pas reprise par la maquette) |
| Options - Incontournables | `option_incontournable_texte_intro`, `option_incontournable_photos_mobile`, `option_incontournable_photos_desktop` | Page de rubrique : chapô / contenu |
| Options - JO 2024 | `option_rugby2023_texte_intro`, `option_rugby2023_photos_mobile`, `option_rugby2023_photos_desktop` | Non repris (photos / style / JO) |
| Options - Menus | `menus`, `elements_de_menu`, `nav_icon`, `titre`, `lien`, `classe_css`, `afficher_sur_la_page_daccueil`, `afficher_dans_le_menu_principal`, `afficher_dans_le_menu_secondaire` | Rubriques : cases « menu » et « accueil », ordre des pages (pas d'icônes dans la maquette) |
| Options - Prendre un verre | `option_lieu_photos_mobile`, `option_lieu_photos_desktop` | Non repris (photos / style / JO) |
| Options - Shopping | `option_shopping_photos_mobile`, `option_shopping_photos_desktop` | Non repris (photos / style / JO) |
| Page classique | `single_baseline`, `single_photos_mobile`, `single_photos_desktop` | Pages d'infos : chapô |
| Prendre un verre | `lieu_baseline`, `lieu_description`, `lieu_adresse`, `lieu_les_petits_plus`, `lieu_petit_plus`, `lieu_itineraire`, `lieu_more`, `lieu_photos_mobile`, `lieu_photos_desktop` | Quartier : chapô, contenu, infos pratiques, bars |
| Shopping | `shopping_photos_mobile`, `shopping_photos_desktop`, `shopping_items`, `shopping_nom`, `shopping_baseline`, `shopping_description`, `shopping_itineraire` | Non repris (photos / style / JO) |
| Style (CSS) | `style_object_position` | Non repris (photos / style / JO) |

Les **galeries photos** (mobile / desktop) de chaque fiche ne sont pas affichées par la maquette ; les photos restent disponibles dans [`../images/`](../images/).

## SEO (Yoast)

- Titre et meta description personnalisés uniquement sur l'accueil (FR, EN, ES) — repris par l'import du thème.
- `noindex` sur les mentions légales (FR, EN) — repris.
- Les autres pages utilisent les réglages Yoast par défaut. Le site entier est actuellement en `noindex, nofollow`.

## Images

- 795 médias ; 89 avec texte alternatif, 169 avec légende (souvent le crédit « © Photographe / LVAN »).
- [`../images/_credits.json`](../images/_credits.json) : alt / légende / titre pour 111 des images téléchargées.

## Redirections

Le plugin Redirection ne contient aucune règle. [`../../theme/redirections.csv`](../../theme/redirections.csv) liste les redirections 301 à créer sur le nouveau site (29 règles) : anciens slugs mémorisés par WordPress, slugs corrigés, pages événementielles et JO 2024 non reprises.
