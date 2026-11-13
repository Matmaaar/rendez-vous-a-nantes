# Déploiement sur le site live (sans préprod)

Trois phases, **un seul changement à la fois**, avec une sauvegarde avant chacune et une vérification après :

1. **Changer de thème** (ce document, étapes 1 à 5) : environ 1 h, sans rien mettre à jour d'autre (le thème est testé sur les versions actuelles du site : WordPress 7.1.4, PHP 7.4.33, ACF PRO 6.3.1, WPML 4.9.7).
2. **Mettre à jour les extensions**, quelques jours plus tard si tout va bien (voir « Phase 2 »).
3. **Passer PHP en 8.3**, encore quelques jours plus tard (voir « Phase 3 »).

Le site a peu de trafic : on déploie directement, avec une sauvegarde et un retour arrière prêts.

> **Option recommandée, 15 minutes de plus : « préprod express ».** Installer l'extension gratuite **WP Staging**, cloner le site (il crée une copie dans un sous-dossier, accessible aux seuls administrateurs), dérouler les étapes 2 à 5 sur la copie et regarder les pages EN / ES. Le thème a été testé avec ACF PRO et WPML aux versions du site, mais pas avec les anciens contenus du vrai site : c'est ce que la copie permet de vérifier. Puis refaire les mêmes étapes sur le live.

## 1. Avant

- [ ] **Sauvegarde complète** (base de données + fichiers) : Réglages → Sauvegardes UpdraftPlus → « Sauvegarder maintenant », **base de données cochée** (elle n'est plus sauvegardée automatiquement depuis mars 2024). Vérifier que la sauvegarde du jour apparaît avec le bouton « Base de données ».
- [ ] Noter le **thème actif** actuel (Apparence → Thèmes) : c'est le retour arrière.
- [ ] Vérifier dans **Extensions** si une extension déclare les anciens types de contenu (ex. *Custom Post Type UI*). Si oui, la désactiver : sinon l'import s'arrête (voir étape 3) parce que les adresses `/incontournable/`, `/shopping/`… seraient en conflit.
- [ ] Vérifier qu'**ACF PRO** et **ACF Multilingual** sont actifs (Extensions) : ils servent à la saisie des champs du nouveau thème. Le reste des mises à jour (WPML, ACF…) se fait après la mise en ligne, une étape à la fois.

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

- [ ] **ACF** → Groupes de champs : la liste « Synchronisation disponible » montre les 7 groupes du thème (Page d'accueil, Rubrique, Liste de fiches, Liste d'adresses, Page d'infos, Quartier, Fiche) → les **synchroniser**. Les groupes de l'ancien thème restent dans la liste, inchangés ; ils sont simplement masqués sur les écrans des nouvelles pages.

- [ ] **WPML** → Paramètres → Types de contenu : vérifier que Quartiers, Incontournables, Shopping et Pass Nantes sont « traduisibles », puis « Traduire les slugs » : `incontournable` → `unmissable` / `imprescindibles`, `prendre-un-verre` → `have-a-drink` / `tomar-una-copa`, `24h-de-plus` → `24h-more` / `24h-mas`.
- [ ] **WPML** → Sélecteur de langue : rien à configurer, le thème affiche son propre sélecteur FR · EN · ES.
- [ ] **Redirection** → Import/Export → importer `theme/redirections.csv` (29 règles).
- [ ] Anciennes pages d'accueil événementielles (Marathon 2023, Open de squash, Rendez-vous en France, page japonaise) : les passer en **brouillon** (les redirections renvoient déjà leurs adresses vers l'accueil).
- [ ] Réglages → Lecture : laisser **cochée** « Demander aux moteurs de recherche de ne pas indexer ce site » (décision : le site reste masqué, comme aujourd'hui).
- [ ] Pas de bandeau cookies à ajouter : le site n'utilise pas d'outil de mesure d'audience ni de cookie publicitaire. Si un outil est ajouté un jour, il faudra un bandeau de consentement.
- [ ] Hébergeur : vérifier que la **compression** (gzip ou brotli) et le **cache navigateur** des fichiers statiques (images, CSS, polices) sont activés. C'est ce qui reste à gagner en vitesse, et ça se règle côté serveur, pas dans le thème.
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

## Phase 2 : mettre à jour les extensions (quelques jours après)

Seulement si la phase 1 n'a montré aucun problème.

- [ ] **Sauvegarde complète** (UpdraftPlus, base de données cochée).
- [ ] Extensions → mettre à jour **une par une**, en vérifiant l'accueil et une fiche après chacune : d'abord la suite **WPML** (WPML Multilingual CMS, String Translation, Media Translation, SEO, ACF Multilingual ; licence WPML active nécessaire), puis **ACF PRO** (licence ACF active nécessaire), **Yoast SEO**, **All-In-One Security**, **UpdraftPlus**.
- [ ] Recette rapide : accueil FR / EN / ES, une fiche, un quartier, l'écran de modification d'une fiche dans l'admin.
- **En cas de problème** : restaurer la sauvegarde de cette phase (UpdraftPlus → « Restaurer »).

## Phase 3 : passer PHP en 8.3 (quelques jours après)

PHP 7.4 ne reçoit plus de correctifs de sécurité depuis fin 2022. Le thème fonctionne sous 7.4 et sous 8.3.

- [ ] **Sauvegarde complète** (UpdraftPlus, base de données cochée).
- [ ] Espace client **o2switch** (cPanel) → « Sélectionner une version de PHP » → **8.3** → appliquer.
- [ ] Recette rapide (navigation privée) : accueil FR / EN / ES, une fiche, un quartier, l'admin (tableau de bord, modification d'une fiche). Outils → Santé du site → Informations → Serveur : la version de PHP doit indiquer 8.3.
- **En cas de problème** : remettre **7.4** au même endroit (effet immédiat), puis chercher l'extension en cause.

## Phase 4 : ranger (quand tout est stable)

- [ ] Désactiver les extensions qui ne servaient qu'à l'ancien thème : ACF Font Awesome, ACF Content Analysis for Yoast. Garder **ACF PRO** et **ACF Multilingual** (le nouveau thème s'en sert).
- [ ] Programmer la sauvegarde automatique de la **base de données** : Sauvegardes UpdraftPlus → Réglages → « Planification de la sauvegarde de la base de données » : chaque semaine.

