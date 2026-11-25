# Mise en production par phases

Modèle de `DEPLOIEMENT.md`, à adapter au site. À guider avec l'utilisateur **une étape à la fois**, en lui demandant le rapport ou une capture aux points marqués 📸.

## Phase 0 (optionnelle) : préprod

WP Staging (gratuit) crée une copie dans un sous-dossier, visible des seuls administrateurs. Utile pour tester l'import sur les vrais anciens contenus. Prévenir des limites (voir `pieges.md`). La supprimer après la mise en ligne.

## Phase 1 : le thème seul (environ 1 h)

1. **Sauvegarde** UpdraftPlus, base de données cochée ; noter le thème actif (= retour arrière).
2. Vérifier qu'aucune extension ne déclare les types de contenu du nouveau thème ; vérifier qu'ACF PRO (et ACF Multilingual si WPML) sont actifs.
3. *Apparence → Thèmes → Ajouter → Téléverser* le zip. Ne pas activer tout de suite. Déposer les polices sous licence (FTP / gestionnaire de fichiers) si disponibles.
4. Activer le nouveau thème (nom différent de l'ancien).
5. *Outils → Import* en **simulation** 📸 → relire avec l'utilisateur → import réel 📸.
6. *ACF → Groupes de champs → Synchronisation disponible* : synchroniser **uniquement** les groupes du thème 📸.
7. *Outils → Redirection → Import/Export* : importer le CSV 📸 (vérifier le nombre de règles).
8. Anciennes pages retirées → **brouillon** (Actions groupées → Modifier → État : Brouillon), dans chaque langue.
9. Vérifier l'indexation (*Réglages → Lecture*) selon la décision prise. Vider les caches.
10. Recette en navigation privée : accueil, chaque rubrique, une fiche par type, pages d'infos, EN et ES via le sélecteur, mobile, 3 redirections.
11. Vérification complémentaire en lecture seule si un connecteur existe : thème actif, page d'accueil, pages publiées / brouillons, quelques redirections avec `curl -I`, balise robots.

Retour arrière : réactiver l'ancien thème (rapide) ou restaurer la sauvegarde (complet).

## Phase 2 : extensions (quelques jours après, si tout va bien)

Sauvegarde, puis mise à jour **une par une** avec vérification de l'accueil et d'une fiche après chacune : la suite multilingue d'abord (licence active nécessaire), puis ACF PRO, SEO, sécurité, sauvegarde.

## Phase 3 : PHP (quelques jours après)

Sauvegarde, puis hébergeur (cPanel → « Sélectionner une version de PHP ») → version stable récente (8.3 en 2026). Recette rapide. En cas de problème : revenir à l'ancienne version au même endroit (effet immédiat).

## Phase 4 : rangement

Désactiver les extensions qui ne servaient qu'à l'ancien thème, programmer la sauvegarde hebdomadaire de la base, supprimer la préprod, publier la version du thème sans l'import, mettre à jour `REPRISE.md`.
