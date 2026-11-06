import re, json, sys, time, urllib.request, urllib.parse, os, hashlib
BASE='https://rdv.levoyageanantes.fr'
OUT=sys.argv[1]
os.makedirs(OUT,exist_ok=True)
seeds=[BASE+'/',BASE+'/en/',BASE+'/es/']
for l in ['fr','en','es']:
    for p in json.load(open(os.path.join(os.path.dirname(OUT),f'raw/pages_{l}.json'))): seeds.append(p['link'])
import glob
for f in glob.glob(os.path.join(os.path.dirname(OUT),'sitemap','*-sitemap.xml')):
    seeds += re.findall(r'<loc>([^<]+)</loc>', open(f).read())
SKIP=re.compile(r'/(wp-content|wp-json|wp-admin|wp-includes|feed|xmlrpc|comments)|\.(jpe?g|png|gif|svg|pdf|webp|css|js|xml|ico|mp4)(\?|$)|\?|#',re.I)
def norm(u):
    u=u.split('#')[0]
    p=urllib.parse.urlsplit(u)
    path=p.path or '/'
    if not path.endswith('/') and '.' not in path.rsplit('/',1)[-1]: path+='/'
    return BASE+path
prev=json.load(open(os.path.join(OUT,'_index.json'))) if os.path.exists(os.path.join(OUT,'_index.json')) else {}
seen={}; queue=[norm(s) for s in seeds]; done=set()
while queue:
    u=queue.pop(0)
    if u in done: continue
    done.add(u)
    fn=hashlib.md5(u.encode()).hexdigest()[:10]+'.html'
    fp=os.path.join(OUT,fn)
    if u in prev and 'file' in prev[u] and os.path.exists(fp):
        seen[u]=prev[u]; html=open(fp).read()
    else:
        html=None
        for attempt in range(8):
            try:
                req=urllib.request.Request(u,headers={'User-Agent':'Mozilla/5.0 (content-archive)'})
                r=urllib.request.urlopen(req,timeout=30); final=r.geturl(); html=r.read().decode('utf-8','replace'); code=r.status
                break
            except urllib.error.HTTPError as e:
                if e.code==429: time.sleep(15*(attempt+1)); continue
                print('ERR',u,e); seen[u]={'error':str(e)}; break
            except Exception as e:
                time.sleep(5)
        if html is None:
            seen.setdefault(u,{'error':'failed'}); print('FAIL',u); continue
        open(fp,'w').write(html)
        seen[u]={'file':fn,'final':final,'status':code}
        print(code,u,'->',final if final!=u else '',flush=True)
        time.sleep(2)
    for h in re.findall(r'href=["\']([^"\']+)["\']',html):
        h=h.replace('&#038;','&').replace('&amp;','&')
        if h.startswith('//'): continue
        if h.startswith('/'): h=BASE+h
        if not h.startswith(BASE) or SKIP.search(h[len(BASE):]): continue
        n=norm(h)
        if n not in done and n not in queue: queue.append(n)
json.dump(seen,open(os.path.join(OUT,'_index.json'),'w'),indent=1)
