# Pièges rencontrés (et la parade)

| Piège | Ce qui se passe | Parade |
| --- | --- | --- |
| Préprod WP Staging dans un sous-dossier sans `.htaccess` | Les adresses jolies de la copie (`/preprod/footing/`) tombent sur le WordPress en ligne, qui redirige vers de vieilles pages ou des images. La préprod semble cassée. | Le dire avant : la préprod sert surtout à tester l'import et l'admin. Naviguer avec `/preprod/?page_id=ID`. Corriger le `.htaccess` demande un accès hébergeur ; ne pas faire installer d'extension pour ça sans accord. |
| Modèle de page de l'ancien thème | L'import échoue sur une page reprise : « Modèle de page non valide ». | Toujours définir `page_template` lors de la reprise (le gabarit du nouveau thème, ou `default`). |
| Fichiers de test oubliés dans le thème | Des groupes ACF de test apparaissent dans « Synchronisation disponible » en production. | Avant de construire le zip, lister le contenu de `acf-json/` et des dossiers générés ; ne garder que ce que le script produit. |
| Fautes du site actuel | « Mercato » (italien) au lieu de « Mercado », « distric »… recopiés dans les titres traduits. | Relire les titres EN/ES importés ; table de corrections dans le script de génération ; signaler le reste à l'utilisateur. |
| Hébergeur qui limite les robots | Crawl du site en ligne : HTTP 429 en série, même en ralentissant. | Crawler en local. En ligne : quelques requêtes espacées seulement, ou laisser l'utilisateur vérifier à la main. |
| Slugs de types de contenu non traduits (WPML) | Les redirections visent `/en/unmissable/…` mais la fiche est à `/en/incontournable/…`. | WordPress redirige souvent seul l'ancienne adresse (vérifier avec `curl -I`). Sinon traduire les slugs dans WPML ou adapter le CSV. |
| Menu ordinateur / mobile | Le menu mobile flottant apparaissait sur ordinateur. | Seuils explicites (mobile ≤ 900 px, menu sur deux lignes entre 901 et 1180 px) et test à chaque largeur. |
| Interface en anglais sur un WordPress anglais | La traduction anglaise du thème se chargeait aussi sur les pages FR. | Filtre `override_load_textdomain` : traductions seulement si la langue WPML courante n'est pas FR. |
| Hook ACF déprécié | `acf/get_field_groups` est déprécié. | Utiliser `acf/load_field_groups` (priorité 30). |
| Date `modified` des JSON ACF | Une date dans le futur laisse « Synchronisation disponible » affichée en permanence. | Le script ne change `modified` que si le contenu change. |
| Connexion Jetpack sur la préprod | Refus (403) sur une copie en sous-dossier. | Connecter Jetpack (ou l'outil MCP) sur le site en ligne, en lecture seule. |
| Base de données non sauvegardée | La sauvegarde automatique ne couvrait plus la base depuis des mois. | Vérifier les réglages UpdraftPlus dès l'état des lieux ; programmer une sauvegarde hebdomadaire de la base en fin de projet. |
| Affirmations trop rapides | Plusieurs explications données de mémoire étaient fausses (ex. « WordPress ne permet pas à un outil externe d'utiliser ton compte »). | Vérifier avant d'affirmer ; dire « je ne suis pas sûr » ; corriger franchement. |
