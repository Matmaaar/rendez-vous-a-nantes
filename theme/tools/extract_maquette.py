"""Extrait les données (rubriques, quartiers, fiches…) et la carte de la maquette HTML.

Usage : python3 -I theme/tools/extract_maquette.py maquette/rdv-nantes-v1.html
Produit theme/tools/maquette.json et theme/rdv-nantes/assets/img/plan-nantes.webp (nécessite node).
"""
import base64, os, re, subprocess, sys, tempfile
HERE = os.path.dirname(os.path.abspath(__file__))
s = open(sys.argv[1]).read()
js = s[s.index('<script>') + 8:s.index('/* ---------- RENDU')]
m = re.search(r'data:image/webp;base64,([A-Za-z0-9+/=]+)', js)
open(os.path.join(HERE, '..', 'rdv-nantes', 'assets', 'img', 'plan-nantes.webp'), 'wb').write(base64.b64decode(m.group(1)))
js = js.replace(m.group(0), 'MAP') + '\nconsole.log(JSON.stringify({CATS,QUARTIERS,LIEUX,LISTES,INFOS,PLANS},null,1));'
with tempfile.NamedTemporaryFile('w', suffix='.js', delete=False) as f:
    f.write(js)
out = subprocess.run(['node', f.name], capture_output=True, text=True, check=True).stdout
open(os.path.join(HERE, 'maquette.json'), 'w').write(out)
os.unlink(f.name)
print('maquette.json + plan-nantes.webp mis à jour')
