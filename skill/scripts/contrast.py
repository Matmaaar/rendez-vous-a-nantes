"""Contraste réel d'un texte clair posé sur une photo : python3 contrast.py <dossier> [couleur-texte]

Lit <dossier>/tiles.json produit par tiles.js. Pour chaque texte, mesure le contraste entre la couleur du
texte (blanc par défaut, ex. "255,255,255") et chaque pixel du fond situé sous le texte, halo compris.
Référence : le 10e centile (10 % des pixels sous le texte sont moins contrastés). Seuil WCAG AA :
4,5:1, ou 3:1 pour le grand texte (>= 24 px, ou >= 18,66 px en gras 700).
"""
import json
import os
import sys

from PIL import Image

folder = sys.argv[1]
fg = tuple(int(x) for x in (sys.argv[2] if len(sys.argv) > 2 else '255,255,255').split(','))


def lum(rgb):
    def ch(c):
        c = c / 255
        return c / 12.92 if c <= 0.03928 else ((c + 0.055) / 1.055) ** 2.4
    r, g, b = rgb[:3]
    return 0.2126 * ch(r) + 0.7152 * ch(g) + 0.0722 * ch(b)


L_FG = lum(fg)


def ratio(px):
    a, b = sorted((L_FG, lum(px)), reverse=True)
    return (a + 0.05) / (b + 0.05)


def need(font):
    size, weight = font.split()
    size, weight = float(size.rstrip('px')), int(float(weight))
    return 3.0 if size >= 24 or (size >= 18.66 and weight >= 700) else 4.5


rows = []
for t in json.load(open(os.path.join(folder, 'tiles.json'))):
    img = Image.open(os.path.join(folder, t['f'])).convert('RGB')
    for tx in t['texts']:
        vals = []
        for x, y, w, h in tx['rects']:
            for yy in range(int(y), int(y + h)):
                for xx in range(int(x), int(x + w)):
                    if 0 <= xx < img.width and 0 <= yy < img.height:
                        vals.append(ratio(img.getpixel((xx, yy))))
        if not vals:
            continue
        vals.sort()
        rows.append(dict(w=t['w'], path=t['path'], sel=tx['sel'], text=tx['text'], font=tx['font'],
                         p10=vals[len(vals) // 10], need=need(tx['font'])))

fails = [r for r in rows if r['p10'] < r['need']]
print(f"{len(rows) - len(fails)}/{len(rows)} textes conformes (WCAG AA selon taille et graisse)")
for r in sorted(rows, key=lambda r: r['p10'] - r['need'])[:15]:
    flag = 'NON CONFORME' if r['p10'] < r['need'] else 'ok'
    print(f"  {flag:12} {r['w']:>4} {r['path']:<28} {r['sel']:<6} {r['font']:<10} {r['p10']:.2f} (seuil {r['need']})  {r['text'][:40]}")
json.dump(rows, open(os.path.join(folder, 'contrast.json'), 'w'), ensure_ascii=False)
