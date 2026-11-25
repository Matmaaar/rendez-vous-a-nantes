---
name: refonte-site-lvan
description: Méthode éprouvée pour refondre et mettre en ligne un site du Voyage à Nantes (LVaN) ou d'un de ses sites satellites (Rendez-vous à Nantes, Machines de l'Île, Château, Estuaire, etc.), de l'état des lieux du site actuel jusqu'à la mise en production par étapes. Utiliser ce skill dès que l'utilisateur parle de refonte, de nouveau site, de nouveau thème WordPress, de reprendre ou moderniser un site existant, de préparer une mise en ligne, une préprod, un import de contenu, ou de présenter un projet web à une agence, même s'il ne dit pas « refonte » (ex. « on refait le site des Machines », « je veux un nouveau thème pour X », « comment je mets ça en prod »).
---

# Refonte de site LVaN : la méthode

Cette méthode vient du projet Rendez-vous à Nantes (rdv.levoyageanantes.fr), refait en novembre 2026 et mis en ligne du premier coup, sans casse. Elle repose sur une idée simple : **on ne change qu'une chose à la fois, on teste sur les vraies versions du site, et on peut toujours revenir en arrière.**

## Avec qui tu travailles

La personne qui pilote n'est pas développeuse et ne connaît pas WordPress en profondeur. Elle doit pouvoir défendre le projet devant une agence ou sa hiérarchie, donc elle a besoin de **comprendre**, pas seulement de recevoir du code.

- Explique en français simple, phrases courtes, sans jargon. Un terme technique inévitable (ACF, WPML, slug, préprod…) s'explique en une ligne, du point de vue de ce que fait concrètement un utilisateur dans l'admin.
- Guide **étape par étape**, une étape à la fois, avec l'endroit exact où cliquer (« Apparence → Thèmes → Ajouter »). Demande une capture d'écran ou le texte d'un rapport avant l'étape suivante quand le résultat compte.
- Avant d'agir, dis ce que tu vas faire et pourquoi. Ne fais pas installer d'extension ni créer de compte sans avoir expliqué le besoin : la personne l'a mal vécu quand c'est arrivé sans prévenir.
- Sur le site en ligne (connecteur WordPress.com / Jetpack, ou autre accès) : **lecture seule** tant que la personne ne demande pas explicitement un changement, et annonce exactement ce qui va changer.
- Ne demande jamais de mot de passe ou de clé dans la conversation.
- Quand tu te trompes, corrige clairement (« j'ai dit X, c'est faux, en réalité Y »).
- Réponses courtes ; les détails vont dans les fichiers du projet.

## Les étapes

Lis `references/methode.md` pour le détail de chaque étape (quoi produire, comment vérifier). En résumé :

1. **État des lieux du site actuel** : versions (WordPress, PHP, extensions, thème) relevées dans *Outils → Santé du site* et *Extensions*, hébergeur, qui a les accès (hébergeur, admin), arborescence (sitemap), contenus FR/EN/ES archivés dans le dépôt, sauvegardes existantes. Ça conditionne tout le reste : le thème doit marcher sur ces versions exactes.
2. **Maquette HTML** validée par l'utilisateur avant toute ligne de WordPress. Les choix de design se règlent là, c'est dix fois moins cher qu'après.
3. **Thème WordPress classique sur mesure** (gabarits PHP, pas de constructeur de pages), champs de saisie en **ACF PRO** (Local JSON versionné), multilingue avec l'outil déjà en place (souvent WPML). Le front lit les métas directement, donc le site s'affiche même sans ACF (utile pour une démo Playground).
4. **Import de contenu rejouable** embarqué dans le thème : mode simulation, reprise des pages existantes par leur adresse (les URL ne bougent pas), aucun doublon si on relance, jamais de suppression d'ancien contenu.
5. **Tests sur un environnement identique au site** (mêmes versions de WordPress, PHP, ACF, WPML), crawl de toutes les pages en FR/EN/ES, ordinateur et mobile.
6. **Audit d'accessibilité** (WCAG 2.1 AA) : axe-core, contraste réel des textes posés sur photos, affichage à 320 px, textes alternatifs, clavier. Voir `references/accessibilite.md` et les scripts de `scripts/`.
7. **Documents de passation** : fiche de reprise (`REPRISE.md`), procédure de mise en ligne (`DEPLOIEMENT.md`), récap projet présentable à une agence.
8. **Mise en production par phases** : voir `references/mise-en-production.md`. Phase 1 = le thème seul ; extensions, PHP et rangement viennent ensuite, chacune avec sa sauvegarde.

Ne saute pas d'étape sans le dire. Si l'utilisateur veut aller plus vite, propose la version courte de l'étape plutôt que de la supprimer.

## Règles qui ont évité les problèmes

- **Sauvegarde complète avec la base de données cochée** avant chaque phase (UpdraftPlus : « Sauvegarder maintenant », vérifier que la ligne du jour a un bouton « Base de données »). Vérifie aussi si la base est sauvegardée automatiquement : sur RDV elle ne l'était plus depuis deux ans.
- **Un seul changement à la fois** : thème, puis extensions une par une, puis PHP. Si ça casse, on sait pourquoi.
- **Simulation avant l'import réel**, et lecture du rapport avec l'utilisateur.
- **Ne rien supprimer** : anciens contenus et anciens groupes ACF restent en base, simplement masqués. Le retour arrière = réactiver l'ancien thème.
- **Dépôt public = ni extensions payantes (ACF PRO, WPML), ni polices sous licence (Suisse Int'l), ni identifiants.** Les polices se déposent à part sur le serveur ; le thème prévoit une police de secours.
- **Regénérer le zip du thème après chaque modification** et monter la version (style.css + constante PHP), sinon l'utilisateur installe une ancienne copie.
- **Pas d'audience mesurée = pas de bandeau cookies** ; à réévaluer si un outil est ajouté.
- Le choix de **PHP 8.3** (et pas la toute dernière) : version stable, supportée plusieurs années, compatible avec les extensions courantes. La dernière sortie est souvent trop récente pour certaines extensions.

## Pièges connus

Lis `references/pieges.md` avant la phase de tests et avant la mise en ligne. Les plus coûteux sur RDV : préprod WP Staging sans `.htaccess` (liens qui partent vers le site en ligne), modèle de page de l'ancien thème qui bloque l'import, fichiers de test oubliés dans le thème, fautes du site actuel recopiées telles quelles dans les traductions, hébergeur qui bloque un crawl trop rapide (HTTP 429).

## Ce qu'il faut livrer à la fin

- Le thème (zip d'installation + code dans le dépôt), versionné.
- `REPRISE.md` à jour : où est quoi, décisions prises (à ne pas rediscuter sans demander), versions du site, ce qui reste à faire.
- `DEPLOIEMENT.md` : la procédure de mise en ligne, cases à cocher, retour arrière.
- Le fichier de redirections (CSV pour l'extension Redirection) pour toutes les anciennes adresses qui changent ou disparaissent.
- Si l'utilisateur doit présenter le projet : un récap clair à la charte LVaN (skill `charte-lvan`), orienté roadmap et qualité, sans détour par « c'est l'IA qui a fait ».
