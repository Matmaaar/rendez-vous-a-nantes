# Méthode détaillée

## 1. État des lieux du site actuel

Objectif : savoir exactement sur quoi le nouveau thème va tourner, et ne rien perdre.

- Demander des captures de *Outils → Santé du site → Informations* (sections WordPress, Serveur) et de *Extensions*. Noter : version WordPress, PHP, hébergeur, préfixe de tables, extensions actives avec versions, thème actif, permaliens, langue, indexation.
- Comparer avec les dernières versions disponibles et le dire simplement (« WordPress est à jour, PHP a une grande version de retard »).
- Qui a les accès : admin WordPress (liste des utilisateurs), hébergeur (souvent l'agence ou une personne précise). Le savoir tôt évite de bloquer en fin de projet.
- Archiver le contenu du site actuel dans le dépôt (`contenu/`) : pages FR/EN/ES, images, sitemap, crédits photo, export WordPress si disponible (sauvegarde UpdraftPlus, export XML). C'est la source de l'import ; ne plus y toucher ensuite.
- Repérer ce qui disparaît (pages événementielles, rubriques mortes) : chaque adresse qui disparaît ou change aura une redirection 301.

Livrable : section « Site actuel » de `REPRISE.md`.

## 2. Maquette HTML

- Une page HTML statique qui montre toutes les gabarits (accueil, rubrique, liste, fiche, page d'infos), avec le vrai contenu FR.
- Les décisions de design se prennent ici et se notent dans `REPRISE.md` (« Décisions prises ») : couleurs, typo, menu mobile, comportement des grilles, ce qui est retiré. Une décision notée ne se rediscute pas sans demander.
- Vérifier la maquette à plusieurs largeurs (390, 900, 1180, 1280 px) : les seuils du menu ordinateur / mobile sont une source classique d'erreur.

## 3. Thème WordPress

Choix par défaut, à adapter si le site actuel impose autre chose :

- **Thème classique** (gabarits PHP + CSS), pas de constructeur de pages. Gutenberg désactivé pour les pages gérées par le thème (filtre `use_block_editor_for_post_type`) : l'équipe remplit des champs, la mise en page reste propre.
- **ACF PRO** pour les champs (si la licence existe déjà sur le site) : groupes en Local JSON (`acf-json/`), générés par un script pour rester cohérents ; répéteurs pour les listes, champ « Lien » pour les boutons. Le front lit les métas brutes (`<prefixe>_<nom>`, `<prefixe>_<rep>_<n>_<sous>`) : le site s'affiche même sans ACF.
- Masquer les groupes ACF de l'ancien thème sur les écrans des nouveaux contenus (filtre `acf/load_field_groups`), sans les supprimer.
- **Multilingue** avec l'outil déjà en place (WPML le plus souvent) : `wpml-config.xml` dans le thème (types traduisibles, champs à copier ou traduire). Interface du thème écrite en français, traductions EN/ES chargées seulement sur les pages EN/ES.
- Types de contenu propres au thème avec un préfixe (`rdv_…`) ; adresses identiques à celles du site actuel quand c'est possible.
- Polices sous licence chargées depuis un dossier non versionné, police de secours sinon.
- `screenshot.png` (1200×900) et un nom de thème différent de l'ancien (sinon confusion dans *Apparence → Thèmes*).

## 4. Import rejouable

- Contenu et images embarqués dans le thème (`import/content.json`, `import/images/`), produits par un script depuis l'archive et la maquette.
- Écran *Outils → Import* avec **simulation cochée par défaut** et rapport lisible (« reprise de la page existante », « création de », « IMPORT BLOQUÉ »).
- Clé d'import stockée en méta (`_<prefixe>_import_key`) : relancer met à jour, ne duplique pas.
- Reprise des pages existantes par leur slug ; forcer `page_template` (sinon un modèle de l'ancien thème fait échouer la mise à jour : « Modèle de page non valide »).
- Bloquer si un autre plugin déclare déjà les mêmes adresses de type de contenu.
- Corriger les fautes connues du site actuel au moment de la génération (table de corrections dans le script), pas à la main après.
- Une fois en ligne et stable, une version suivante du thème retire l'import.

## 5. Tests

- Environnement local identique au site : mêmes versions de WordPress et de PHP (image Docker `mirror.gcr.io/library/php:<version>-cli` si Docker Hub limite), mêmes versions d'ACF PRO et WPML (extensions payantes tirées de la sauvegarde du site, jamais dans le dépôt).
- Configurer WPML sans assistant si besoin (endpoints `WPML\Setup\Endpoint\…`), serveur PHP avec plusieurs workers (`PHP_CLI_SERVER_WORKERS=4`) pour les appels REST internes.
- Crawl de toutes les pages FR/EN/ES à 1280 et 390 px (`scripts/crawl.js`) : statut HTTP, erreurs JS, images cassées, débordement horizontal, liens internes cassés, textes provisoires (« à mettre à jour », « lorem »).
- Tester l'import deux fois de suite (pas de doublon) et sur une copie contenant les anciens contenus si possible.
- Démo partageable : WordPress Playground avec un `blueprint.json` (FR seulement, police de secours).

## 6. Accessibilité

Voir `accessibilite.md`.

## 7. Passation et présentation

- `REPRISE.md` : à lire en premier par la prochaine personne (ou la prochaine conversation). Où est quoi, décisions, versions, environnement de test, ce qui reste.
- `DEPLOIEMENT.md` : voir `mise-en-production.md`.
- Pour une agence : récap à la charte LVaN (skill `charte-lvan`), sections Synthèse, Roadmap (ce qui est fait, coché), Architecture, Recette/tests, Environnement (versions, extensions, hébergement), Mise en production, Points ouverts. Ton : projet maîtrisé et documenté.
- Préparer la personne aux questions probables : versions, pourquoi tel choix (ACF, thème classique, PHP 8.3), base de données (l'import écrit dans la base existante, rien à migrer ni à préfixer), sauvegarde, retour arrière, qui maintient ensuite.

## 8. Mise en production

Voir `mise-en-production.md`.
