# Audit d'accessibilité (WCAG 2.1 AA)

À faire en local, avant la mise en ligne. Les scripts sont dans `scripts/` (Node + Playwright ; `npm i axe-core` pour le premier).

1. **axe-core** sur une page de chaque gabarit, en FR/EN/ES, à 1280 et 390 px : `node scripts/axe.js <url-de-base> <fichier-de-chemins>`. Objectif : 0 infraction. Les points « à vérifier » (incomplete) sur le contraste des textes posés sur photo sont normaux : axe ne sait pas mesurer, d'où l'étape 2.
2. **Contraste réel des textes sur photos** : `node scripts/tiles.js …` capture chaque case avec le texte masqué (halo conservé) et la position du texte ; `python3 scripts/contrast.py <dossier>` mesure le fond pixel par pixel sous le texte et compare au seuil WCAG (4,5:1, ou 3:1 pour le grand texte : ≥ 24 px, ou ≥ 18,66 px en gras). Le 10e centile le plus sombre sert de référence. Parade la plus simple si ça échoue : passer les titres en gras (ils deviennent « grand texte »), renforcer le halo.
3. **320 px** (zoom fort / petits téléphones) : aucune page ne doit défiler horizontalement (`scripts/crawl.js` mesure le débordement). Parade : `overflow-wrap:break-word; hyphens:auto` et taille réduite des grands titres sous 360 px.
4. **Textes alternatifs** : photos informatives = alt (celui de l'image, sinon le titre de la fiche) ; photos décoratives = `alt=""` assumé.
5. **Clavier** : lien d'évitement, focus visible, menu mobile ouvrable au clavier, `aria-expanded`, fermeture par Échap, retour du focus sur le bouton.
6. Hors outils : un vrai test au lecteur d'écran reste à faire ; vérifier en interne l'obligation de déclaration d'accessibilité (RGAA) selon le statut de la structure.

Noter le résultat chiffré dans `REPRISE.md` (ex. « axe : 0 infraction sur 18 pages × 2 largeurs ; 160/160 textes sur photo conformes, plus faible 4,04:1 »).
