import json, os, re, sys, time, urllib.request, urllib.parse
HTML_DIR, OUT = sys.argv[1], sys.argv[2]
idx = json.load(open(os.path.join(HTML_DIR, '_index.json')))
urls = set()
for info in idx.values():
    if 'file' not in info: continue
    h = open(os.path.join(HTML_DIR, info['file'])).read()
    for u in re.findall(r'https://rdv\.(?:levoyageanantes\.fr|nantes-tourisme\.com)/wp-content/(?:uploads|themes/rdvnantes/img)/[^"\'\)\s]+?\.(?:jpe?g|png|gif|svg|webp)', h, re.I):
        urls.add(u)
print(len(urls), 'images', flush=True)
manifest = {}; done_ = set()
for u in sorted(urls):
    u = u.replace('rdv.nantes-tourisme.com', 'rdv.levoyageanantes.fr')
    rel = urllib.parse.unquote(u.split('/wp-content/', 1)[1])
    rel = rel.replace('themes/rdvnantes/img/', 'theme/')
    fp = os.path.join(OUT, rel)
    manifest[u] = 'images/' + rel
    if u in done_: continue
    done_.add(u)
    if os.path.exists(fp): continue
    os.makedirs(os.path.dirname(fp), exist_ok=True)
    for a in range(8):
        try:
            req = urllib.request.Request(urllib.parse.quote(u, safe=':/%'), headers={'User-Agent': 'Mozilla/5.0 (content-archive)'})
            open(fp, 'wb').write(urllib.request.urlopen(req, timeout=60).read()); print('OK', rel, flush=True); break
        except urllib.error.HTTPError as e:
            if e.code == 429: time.sleep(15 * (a + 1)); continue
            print('ERR', e.code, u, flush=True); manifest[u] = None; break
        except Exception as e:
            time.sleep(5)
    time.sleep(1.5)
json.dump(manifest, open(os.path.join(OUT, '_manifest.json'), 'w'), ensure_ascii=False, indent=1)
