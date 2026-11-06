# Contenu du site actuel — rdv.levoyageanantes.fr

Export complet du site **Rendez-vous à Nantes** (https://rdv.levoyageanantes.fr/), en **français, anglais et espagnol**, pour alimenter la refonte.

Extraction réalisée le 06/11/2026 par crawl du site public (WordPress + WPML, thème `rdvnantes`).

## Organisation

| Dossier / fichier | Contenu |
| --- | --- |
| [`PLAN-DU-SITE.md`](PLAN-DU-SITE.md) | Arborescence + tableau de correspondance FR ↔ EN ↔ ES de chaque page |
| `fr/`, `en/`, `es/` | Une page = un fichier Markdown, rangé selon l'URL d'origine (`/incontournable/le-musee-darts/` → `fr/incontournable/le-musee-darts.md`) |
| `data/pages.json` | Toutes les pages en données structurées (titre, accroche, texte, infos pratiques, liste, boutons, images, menu, traductions, SEO…) — pratique pour un import |
| `images/` | Toutes les images du site (uploads WordPress + pictos/logos du thème dans `images/theme/`). `images/_manifest.json` = URL d'origine → fichier local |
| `html-brut/` | HTML d'origine de chaque page, pour référence |

## Format d'une page Markdown

- **En-tête (front matter)** : URL, langue, type de contenu, `<title>`, meta description, image de partage, date de dernière modification, liens vers les traductions.
- `# Titre` puis `> accroche` (sous-titre) et le texte.
- **Infos pratiques** (fiches lieux) : adresse, tarifs, téléphone, durée.
- **Liste** (pages rubriques) : les fiches de la rubrique, numérotées dans l'ordre du site.
- **Boutons / liens** : boutons de bas de page (Liste, Itinéraire Google Maps, + d'infos, Suivant).
- **Images (diaporama)** : photos du carrousel avec leur texte alternatif.
- Pages d'accueil : texte du splash, image de fond et menu.

## Structure du site (FR)

- **Accueil** (splash + menu)
  - **Prendre un verre** — 4 quartiers / ambiances
  - **Manger** — renvoi vers *Les Tables de Nantes* + idées pour le lundi soir
  - **Incontournables** — 10 fiches lieux
  - **Footing** — parcours running
  - **Shopping** — 5 fiches
  - **Besoin d'aide ?** — accueil du Voyage à Nantes, horaires, contacts
  - **Acheter le Pass Nantes** (`/24h-de-plus/`) — Pass Nantes, ligne verte, plages
  - **Se déplacer** (`/access/`)
  - Mentions légales, politique cookies
- **Pages d'accueil événementielles** (même gabarit que l'accueil, menu propre) : Marathon de Nantes 2023 (FR/EN/ES), Open de squash 2022 (FR), Rendez-vous en France (EN), « Welcome » japonais (EN).

## Points relevés pendant l'extraction

- Langues : FR 35 pages, EN 35, ES 33. Sans traduction : *cookiespolicy* et *opensquash2022* (FR seulement), *rendezvousenfrance* et *welcome-to-japanese* (EN seulement).
- Certains titres ne sont pas traduits : la rubrique Pass Nantes s'appelle « Acheter le Pass Nantes » dans les 3 langues.
- Coquilles dans les slugs : `/shopping/uartier-decre/`, `/es/shopping/mercato-detalensac/`, `/en/unmissable/1279/` (Lieu Unique) ; titre ES « Shoppping ».
- Lien cassé : `/24h-mas/` (404) est référencé sur le site.
- Quelques images pointent encore vers l'ancien domaine `rdv.nantes-tourisme.com` ; elles ont été récupérées depuis `rdv.levoyageanantes.fr` (même chemin).
- Le site est en `noindex, nofollow` ; la meta description est la même sur la plupart des pages.
- Infos datées à mettre à jour : horaires d'été « Juillet – Septembre 2025 », Marathon 2023, Open de squash 2022.

## Relancer l'extraction

Scripts dans [`outils/`](outils/) (Python 3, `beautifulsoup4`, `lxml`, `markdownify`) : `crawl.py` (aspire le HTML), `extract.py` (HTML → Markdown + JSON), `images.py` (télécharge les images), `index.py` (génère `PLAN-DU-SITE.md`). Le serveur limite le débit (erreurs 429), d'où des pauses entre requêtes.
