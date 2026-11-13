# Fiche de reprise — refonte Rendez-vous à Nantes

À lire en premier pour reprendre le projet dans une nouvelle conversation. Branche de travail : `claude/elegant-ride-bswwdv` (tout est poussé).

## Le projet

Refonte de https://rdv.levoyageanantes.fr/ (WordPress + WPML + Yoast, ancien thème `rdvnantes` d'une agence). Le site est peu fréquenté et **reste masqué aux moteurs de recherche**. La nouvelle version est un thème WordPress fait maison à partir de la maquette V1, à présenter à l'ancienne agence avant mise en ligne.

## Où est quoi

| Chemin | Contenu |
| --- | --- |
| `contenu/` | Archive du site actuel FR/EN/ES (pages, images, sitemap, crédits, export WordPress exploité, contenus non publiés). Ne pas modifier, sert de source. |
| `maquette/rdv-nantes-v1.html` | Maquette d'origine. |
| `theme/rdv-nantes/` | **Le thème.** `theme/rdv-nantes.zip` = zip d'installation (à régénérer après chaque modif, polices exclues). |
| `theme/tools/build_content.py` | Génère `theme/rdv-nantes/import/content.json` + images d'import (FR = maquette, EN/ES = site actuel). |
| `theme/README.md` | Doc du thème (édition, choix de mise en page). |
| `theme/DEPLOIEMENT.md` | Procédure de mise en ligne sur le site actuel. |
| `theme/TRADUCTIONS-A-RELIRE.md` | Textes EN/ES créés pour la maquette, à faire relire. |
| `theme/redirections.csv` | 29 redirections 301 à importer (plugin Redirection). |
| `blueprint.json` | Démo WordPress Playground. Lien : `https://playground.wordpress.net/?mode=seamless&blueprint-url=https://raw.githubusercontent.com/matmaaar/rendez-vous-a-nantes/claude/elegant-ride-bswwdv/blueprint.json` (FR seulement, police Inter, sans barres). |

## Décisions prises (ne pas revenir dessus sans demander)

- **Arborescence** = sitemap du site actuel, **sans** JO 2024 / Rugby / pages événementielles. URL actuelles conservées.
- **Mosaïque photo** pour toutes les grilles (accueil, Prendre un verre, Incontournables, Shopping, Pass) : photo 3/2, cases collées, **sans numéros**, titre + accroche en blanc sur un **halo sombre diffus** (pas de dégradé ni de cartouche). 4 colonnes à partir de 8 cases, sinon 3.
- Fiches : **photo de la case** en tête, encadré d'infos aligné sur le haut de la photo. **Aucun surtitre** (ni rubriques ni fiches).
- **Manger** : liste illustrée (photo, nom, description, bouton), photos du guide Les Tables de Nantes ; **Maison Bagarre retiré** (disparu du guide).
- **Pas de carte** sur Prendre un verre ; « X min à pied » retiré. **Pas de parcours** « Visiter Nantes à pied » sur l'accueil.
- **Couleurs** : plus de couleur d'accent (ni jaune, ni vert, ni vert fluo). Un seul fond de mise en avant `--accent: #e6e6f2` (provisoire, choisi par l'utilisateur). Le **noir en aplat a été jugé trop agressif**.
- **Survols** sobres (soulignement noir, zoom photo, gris), pas de vert.
- **En-tête** en haut de page, **pas collant**. **Mobile/tablette** : pas de menu en haut, **pastille arrondie « ☰ Menu » flottante en bas à droite** qui fait monter un panneau (rubriques + Pass Nantes).
- **Police Suisse Int'l** (licence web LVaN) : chargée depuis `theme/rdv-nantes/assets/fonts/` si les WOFF2 y sont ; **jamais dans le repo** (public). Inter en secours.
- **Pass Nantes** : 27/36/44/67 € au lieu de 30/40/49/75 €, promo −10 % jusqu'au 31 mars 2027.
- **Pas de mesure d'audience** → pas de bandeau cookies.
- Interface du thème écrite en **français** : les traductions EN/ES (`languages/`) ne se chargent que sur les pages EN/ES de WPML (un WordPress en anglais affiche bien le FR en français).
- **Champs en ACF PRO** (décision du 13 nov. 2026, pour que la personne qui reprendra le site puisse ajouter des cases en cliquant) : 7 groupes en Local JSON (`theme/rdv-nantes/acf-json/`, générés par `theme/tools/build_acf.py`), listes en répéteurs, boutons en champs « Lien ». L'affichage lit les métas directement : le site marche sans ACF (Playground). Groupes ACF de l'ancien thème masqués sur nos écrans, pas supprimés.
- Multilingue : **WPML** (comme le site actuel). Sans WPML (Playground, test local), seul le FR existe et EN/ES sont grisés dans le sélecteur. La compatibilité Polylang pour la démo a été proposée puis mise de côté.

## Site actuel (relevé dans Outils → Santé du site, 13 nov. 2026)

WordPress **7.1.4**, PHP **7.4.33** (o2switch, Apache/LiteSpeed, pas d'Imagick), WPML 4.9.7, Yoast 28.7, langue fr_FR, permaliens `/%postname%/`, moteurs de recherche découragés. L'utilisateur a un **compte administrateur**.

Extensions actives (13, mises à jour automatiques toutes désactivées) : WPML 4.9.7 + Media Translation 3.1.2, SEO 2.2.5, String Translation 3.5.4, ACF Multilingual 2.2.4 (toute la suite WPML est en 5.2.0), ACF PRO 6.3.1 (6.8.11 dispo), ACF Font Awesome 6.2.0, ACF Content Analysis for Yoast 3.2, Yoast 28.7 (28.8), AIOS 5.4.10 (5.5.1), UpdraftPlus 1.26.13, Redirection 5.10.1, Classic Editor 1.7.0. Aucune ne déclare les anciens types de contenu (ils viennent de l'ancien thème `rdvnantes`) : l'import ne sera pas bloqué. Redirection et UpdraftPlus déjà là (redirections et sauvegarde prêtes). ACF PRO et ACF Multilingual servent aussi au nouveau thème ; ACF Font Awesome et ACF Content Analysis for Yoast ne servent qu'à l'ancien (à désactiver une fois le thème validé). Procédure en 4 phases (thème, extensions, PHP 8.3, rangement) dans `theme/DEPLOIEMENT.md`. Le thème est testé sur ces versions exactes (PHP 7.4 via Docker `mirror.gcr.io/library/php:7.4-cli`). PHP 7.4 n'a plus de correctifs de sécurité depuis fin 2022 : passer en 8.3 est recommandé, mais à tester d'abord sur une copie (ancien thème et extensions).

## Page de révision pour l'entretien

Faite le 13 nov. 2026 : https://claude.ai/artifact/YGYC7pRjZn236Q6igo66j6 (privée). Onglets : L'essentiel, Vocabulaire (24 mots), Décisions, Comment c'est fait, Lancement (phases 0 à 4), Questions (28, en liste ou une par une, progression gardée dans le navigateur), Parler de l'IA. À tenir à jour si le projet évolue.

## Reste à faire / en attente

- Retour de l'ancienne agence sur la maquette.
- Relecture de `theme/TRADUCTIONS-A-RELIRE.md`.
- Mentions provisoires : dates Cantine du Voyage / guinguettes, horaires d'été de l'Accueil (« à mettre à jour »).
- Mentions légales et page cookies à vérifier par l'équipe ; page Accessibilité facultative.
- 6e restaurant pour Manger (facultatif).
- WPML testé avec les versions du site (import, 97 pages FR/EN/ES). Reste à vérifier sur une copie du vrai site (WP Staging) : anciens contenus, anciens groupes ACF, slugs traduits des fiches.
- 11 fiches EN/ES sans infos pratiques (adresse) alors que la version FR en a : absentes du site actuel. À compléter (traduction des libellés).
- Mise en ligne : l'utilisateur a un compte administrateur (à confirmer : accès FTP pour les polices).

## Tester en local (environnement cloud)

Dernière recette : thème v1.2.0 (ACF) sur **WordPress 7.1.4 / PHP 7.4.33 + ACF PRO + WPML**, les versions du site (novembre 2026), sans erreur ; voir `theme/README.md`, section Tests. WordPress récent : `composer create-project johnpbloch/wordpress-core` (wordpress.org est bloqué par le réseau, donc pas de pack de langue fr_FR : sans incidence pour le thème). WP-CLI : `php vendor/wp-cli/wp-cli/php/boot-fs.php`. Sinon : L'environnement « rendez-vous-a-nantes » autorise `rdv.levoyageanantes.fr` et `lestablesdenantes.fr`. Pour un WordPress de test : `apt-get install mariadb-server wordpress`, WP-CLI via `composer require wp-cli/wp-cli-bundle`, copie de `/usr/share/wordpress`, thème lié en symlink, site en `fr_FR` (sinon l'interface s'affiche en anglais), `wp rdv import` (ou `--dry-run`), serveur `php -S localhost:8080`. Captures et contrôles avec Playwright (préinstallé). Après chaque modif du thème : `cd theme && zip -qr rdv-nantes.zip rdv-nantes -x 'rdv-nantes/assets/fonts/*'`.

### Environnement de test complet (ACF PRO + WPML)

- Extensions du site : sauvegarde UpdraftPlus « Extensions » du 9 nov. 2026 dans le Google Drive de l'utilisateur (`backup_2026-11-09-0926_…-plugins.zip`, 44 Mo). Téléchargement via `drive.usercontent.google.com` (hôte autorisé dans l'environnement) avec un partage par lien **temporaire** (à retirer ensuite). **Jamais dans le dépôt** (extensions payantes).
- PHP 7.4 : image Docker `mirror.gcr.io/library/php:7.4-cli` (+ `docker-php-ext-install mysqli`), serveur `PHP_CLI_SERVER_WORKERS=8 php -S localhost:8080` (plusieurs processus : WPML s'appelle lui-même). `WP_HTTP_BLOCK_EXTERNAL` à `true` dans wp-config (sinon WPML attend ses serveurs). WP-CLI tourne avec le PHP 8.3 de la machine.
- WPML sans l'assistant : `make( \WPML\Setup\Endpoint\SetOriginalLanguage::class )->run( wpml_collect( [ 'languageCode' => 'fr' ] ) )`, puis `SetSecondaryLanguages` (`en`, `es`), `AddressStep` (`directory`), `FinishStep`, via `wp eval-file --user=admin`.

## Habitudes de travail avec l'utilisateur

- Échanges en français, réponses courtes et concrètes.
- Pour les choix visuels : **d'abord un test HTML local** (page autonome envoyée dans la conversation, plusieurs variantes, en contexte réel), puis intégration dans le thème seulement après validation. Ne pas pousser un essai sans accord.
- Toujours vérifier (captures 1280 / 390 px, pas de débordement mobile, pas d'erreur PHP) avant de pousser.
