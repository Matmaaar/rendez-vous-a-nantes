# Polices Suisse Int'l

Déposer ici les fichiers web (non versionnés : le repo est public, la police est sous licence) :

- `SuisseIntl-Regular.woff2` (400)
- `SuisseIntl-RegularItalic.woff2` (400 italique)
- `SuisseIntl-Medium.woff2` (500)
- `SuisseIntl-SemiBold.woff2` (600)
- `SuisseIntl-Bold.woff2` (700)

Le thème les charge automatiquement s'ils sont présents ; sinon il affiche Inter.
Conversion depuis les OTF : `python3 -m fontTools.ttLib.woff2 compress SuisseIntl-Regular.otf`
(paquet `fonttools` + `brotli`).
