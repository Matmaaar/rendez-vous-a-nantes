---
name: refonte-site-web
description: Méthode complète et éprouvée pour refondre un site web existant (WordPress en priorité) et le mettre en ligne sans casse, de l'état des lieux jusqu'à la mise en production par étapes, avec la documentation pour l'équipe. Utiliser ce skill dès que l'utilisateur parle de refonte, de nouveau site, de nouveau thème, de reprendre ou moderniser un site existant, de migrer du contenu, de préparer une préprod, une mise en ligne, un audit d'accessibilité avant lancement, ou de présenter un projet web à une agence ou une direction, même sans dire « refonte » (ex. « on refait le site de l'asso », « je veux un nouveau thème », « comment je mets ça en prod sans tout casser »).
---

# Refonte de site : la méthode

Cette méthode vient d'une refonte réelle (site WordPress multilingue, hébergement mutualisé, piloté par une personne non développeuse), mise en ligne du premier coup sans incident. Elle tient en une idée : **on ne change qu'une chose à la fois, on teste sur les vraies versions du site, et on peut toujours revenir en arrière.**

Elle s'applique à n'importe quelle structure. Aucun style graphique n'est imposé : les documents produits sont sobres et lisibles, et ne reprennent la charte d'un client que s'il la fournit ou la demande.

## La personne avec qui tu travailles

Elle n'est souvent pas développeuse. Elle doit pouvoir comprendre et défendre le projet (devant une agence, une direction, un client), pas seulement recevoir du code. Adapte le niveau à ce qu'elle montre ; par défaut :

- Explique en français simple, phrases courtes. Un terme technique inévitable (extension, champ personnalisé, slug, préprod, redirection…) s'explique en une ligne, du point de vue de ce qu'on fait concrètement à l'écran.
- Guide **étape par étape**, une étape à la fois, avec le chemin exact dans les menus. Demande une capture ou le texte d'un rapport avant de continuer quand le résultat compte.
- Avant d'agir, dis ce que tu vas faire et pourquoi. Ne fais pas installer d'extension ni créer de compte sans avoir expliqué le besoin et obtenu son accord.
- Sur le site en ligne : **lecture seule** tant qu'elle ne demande pas explicitement un changement, et annonce exactement ce qui va changer.
- Ne demande jamais de mot de passe ou de clé dans la conversation.
- Quand tu te trompes, corrige franchement. Vérifie plutôt que d'affirmer de mémoire.
- Réponses courtes ; le détail va dans les documents du projet.

## Les étapes

Détail de chaque étape (quoi produire, comment vérifier) : `references/methode.md`.

1. **État des lieux du site actuel** : versions (CMS, PHP, extensions, thème), hébergeur, qui détient les accès, arborescence, contenus archivés, état des sauvegardes. Le nouveau site doit marcher sur ces versions exactes.
2. **Maquette HTML** validée avant toute intégration : les choix de design se règlent là, c'est bien moins cher qu'après.
3. **Thème sur mesure** simple à maintenir : gabarits + champs de saisie structurés (ACF ou équivalent), multilingue avec l'outil déjà en place.
4. **Import de contenu rejouable** : simulation d'abord, reprise des pages existantes (les adresses ne bougent pas), pas de doublon si on relance, aucune suppression.
5. **Tests sur un environnement identique au site**, toutes les pages, toutes les langues, ordinateur et mobile (`scripts/crawl.js`).
6. **Audit d'accessibilité** WCAG 2.1 AA : `references/accessibilite.md` et `scripts/`.
7. **Documentation** (voir plus bas) : guide pour l'équipe, fiche de reprise, procédure de mise en ligne, récap projet.
8. **Mise en production par phases** : `references/mise-en-production.md`. D'abord le thème seul ; extensions, PHP et rangement ensuite, chacun avec sa sauvegarde.

Ne saute pas d'étape sans le dire. Si l'utilisateur veut aller vite, propose la version courte de l'étape plutôt que de la supprimer.

## La documentation fait partie du livrable

Un site sans documentation dépend de la personne qui l'a fait. Prévois ces documents dès le début et tiens-les à jour au fil du projet, pas à la fin :

- **Guide d'utilisation pour l'équipe** (`references/guide-equipe.md`) : comment modifier un texte, changer une photo, ajouter une fiche, traduire, publier, quoi ne pas toucher. Écrit pour quelqu'un qui n'a jamais ouvert l'admin.
- **Fiche de reprise** (`REPRISE.md`) : à lire en premier par la personne ou la conversation suivante. Où est quoi, décisions prises (à ne pas rediscuter sans demander), versions du site, environnement de test, ce qui reste à faire.
- **Procédure de mise en ligne** (`DEPLOIEMENT.md`) : étapes à cocher, sauvegardes, retour arrière.
- **Récap projet** pour une agence, une direction ou un client : synthèse, feuille de route (fait / à faire), architecture, tests réalisés, environnement technique, mise en production, points ouverts. Le ton montre un projet maîtrisé et documenté.

Format : demande à l'utilisateur (document partageable, page HTML, fichier dans le projet). Style sobre par défaut : texte noir sur fond clair, une seule couleur d'accent, titres clairs, navigation simple.

## Règles qui évitent les problèmes

- **Sauvegarde complète, base de données comprise**, avant chaque phase ; vérifier qu'elle apparaît bien. Vérifier aussi dès l'état des lieux que la base est sauvegardée automatiquement (c'est souvent cassé depuis longtemps sans que personne le sache).
- **Un seul changement à la fois** : thème, puis extensions une par une, puis PHP.
- **Simulation avant l'import réel**, rapport relu avec l'utilisateur.
- **Ne rien supprimer** pendant la refonte : anciens contenus et anciens réglages restent en base, masqués. Retour arrière = réactiver l'ancien thème.
- **Dépôt de code public = ni extensions payantes, ni polices sous licence, ni identifiants.** Polices déposées à part sur le serveur, police de secours prévue.
- **Regénérer le paquet d'installation et monter le numéro de version** après chaque modification.
- Chaque adresse qui disparaît ou change reçoit une **redirection 301**.
- **PHP** : viser une version stable récente plutôt que la toute dernière, souvent trop neuve pour certaines extensions.
- Cookies : pas de mesure d'audience ni de publicité = pas de bandeau obligatoire ; à réévaluer si un outil est ajouté.

## Pièges connus

Lire `references/pieges.md` avant les tests et avant la mise en ligne : préprod en sous-dossier sans `.htaccess`, modèles de page de l'ancien thème qui bloquent l'import, fichiers de test oubliés dans le livrable, fautes du site actuel recopiées dans les traductions, hébergeur qui bloque les vérifications automatiques trop rapides, etc.
