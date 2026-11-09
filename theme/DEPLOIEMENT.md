# Déploiement sur le site live (sans préprod)

Durée : environ 1 h. Le site a peu de trafic : on déploie directement, avec une sauvegarde et un retour arrière prêts.

> **Option recommandée, 15 minutes de plus : « préprod express ».** Installer l'extension gratuite **WP Staging**, cloner le site (il crée une copie dans un sous-dossier, accessible aux seuls administrateurs), dérouler les étapes 2 à 5 sur la copie et regarder les pages EN / ES. C'est le seul moyen de tester WPML avant le live : le thème n'a jamais pu être testé avec WPML (payant). Puis refaire les mêmes étapes sur le live.

## 1. Avant

- [ ] **Sauvegarde complète** (base de données + fichiers) : via l'hébergeur, ou l'extension UpdraftPlus. Vérifier qu'elle est téléchargeable.
- [ ] Noter le **thème actif** actuel (Apparence → Thèmes) : c'est le retour arrière.
- [ ] Vérifier dans **Extensions** si une extension déclare les anciens types de contenu (ex. *Custom Post Type UI*). Si oui, la désactiver : sinon l'import s'arrête (voir étape 3) parce que les adresses `/incontournable/`, `/shopping/`… seraient en conflit.
- [ ] Mettre à jour WordPress, WPML et Yoast si besoin.

## 2. Installer le thème

- [ ] Apparence → Thèmes → Ajouter → Téléverser `theme/rdv-nantes.zip`. **Ne pas encore l'activer.**
- [ ] Déposer les polices Suisse Int'l (`suisse-intl-web.zip`, fourni à part, non versionné) dans `wp-content/themes/rdv-nantes/assets/fonts/` (FTP ou gestionnaire de fichiers de l'hébergeur).

## 3. Activer et importer

- [ ] Activer le thème « Rendez-vous à Nantes ».
- [ ] Outils → Import Rendez-vous à Nantes : laisser **« Simulation » cochée** et lancer. Lire le rapport :
  - « reprise de la page existante » : les pages actuelles (Manger, Aide, Mentions légales…) seront mises à jour, **leurs adresses ne changent pas** ;
  - « création de » : nouvelles pages et fiches ;
  - « IMPORT BLOQUÉ » : une extension déclare encore un ancien type de contenu (étape 1).
- [ ] Décocher « Simulation » et relancer. L'import est rejouable sans créer de doublons.

Ce que fait l'import : pages, rubriques et fiches FR / EN / ES (reliées entre elles sous WPML), photos, page d'accueil, permaliens `/%postname%/`. Les anciens contenus (anciennes fiches incontournables, bars, shopping, JO…) **ne sont ni modifiés ni supprimés** : ils restent en base, simplement plus affichés.

## 4. Réglages

- [ ] **WPML** → Paramètres → Types de contenu : vérifier que Quartiers, Incontournables, Shopping et Pass Nantes sont « traduisibles », puis « Traduire les slugs » : `incontournable` → `unmissable` / `imprescindibles`, `prendre-un-verre` → `have-a-drink` / `tomar-una-copa`, `24h-de-plus` → `24h-more` / `24h-mas`.
- [ ] **WPML** → Sélecteur de langue : rien à configurer, le thème affiche son propre sélecteur FR · EN · ES.
- [ ] **Redirection** → Import/Export → importer `theme/redirections.csv` (29 règles).
- [ ] Anciennes pages d'accueil événementielles (Marathon 2023, Open de squash, Rendez-vous en France, page japonaise) : les passer en **brouillon** (les redirections renvoient déjà leurs adresses vers l'accueil).
- [ ] Réglages → Lecture : « Demander aux moteurs de recherche de ne pas indexer ce site » selon la décision prise (le site actuel n'est pas indexé).
- [ ] Vider les caches (extension de cache, hébergeur, CDN).

## 5. Recette (15 minutes, en navigation privée)

- [ ] FR : accueil, Prendre un verre + 1 quartier, Manger, Incontournables + 1 fiche, Footing, Shopping + 1 fiche, Se déplacer, Aide, Pass Nantes + 1 fiche, Mentions légales.
- [ ] EN et ES : accueil, 1 rubrique, 1 fiche ; le sélecteur FR · EN · ES mène bien à la page traduite.
- [ ] 3 redirections : `/shopping/uartier-decre/`, `/marathon-nantes-2023/`, `/jo-paris-2024/`.
- [ ] Sur un téléphone : accueil et une fiche.
- [ ] Police Suisse Int'l affichée (sinon : fichiers absents du dossier `assets/fonts/`).

## Retour arrière

- **Rapide** : réactiver l'ancien thème. Les anciens contenus n'ont pas été touchés ; seules les pages reprises (Manger, Aide, Footing, Se déplacer, Mentions légales, cookies) ont un nouveau contenu.
- **Complet** : restaurer la sauvegarde de l'étape 1.

## Après la mise en ligne

Les corrections se font ensuite directement dans l'admin (textes, photos, ordre des fiches) : voir [`README.md`](README.md), partie « Édition du contenu ».
