// Crawl de recette : node crawl.js <url-de-base> <fichier-de-chemins> [pause-ms] [dossier-captures]
// Le fichier contient un chemin par ligne (/, /rubrique/, /en/…). Vérifie à 1280, 390 et 320 px :
// statut HTTP, erreurs JS, ressources en erreur, images cassées, images sans alt, débordement horizontal,
// nombre de h1, textes provisoires ; puis teste les liens internes trouvés. Résultat dans crawl.json.
// En ligne, mettre une pause (ex. 4000) : les hébergeurs bloquent les robots trop rapides (HTTP 429).
const {chromium} = require('playwright');
const fs = require('fs');
const [base, file, pause = '0', shots] = process.argv.slice(2);
if (!base || !file) { console.error('usage: node crawl.js <url-de-base> <fichier-de-chemins> [pause-ms] [dossier-captures]'); process.exit(1); }
const paths = fs.readFileSync(file, 'utf8').trim().split('\n').map(s => s.trim()).filter(Boolean);
const wait = ms => new Promise(r => setTimeout(r, ms));

(async () => {
  const b = await chromium.launch();
  const seen = new Set(), out = [];
  if (shots) fs.mkdirSync(shots, {recursive: true});
  for (const [w, h] of [[1280, 800], [390, 844], [320, 700]]) {
    const p = await (await b.newContext({viewport: {width: w, height: h}})).newPage();
    const errs = [], bad = [];
    p.on('console', m => { if (m.type() === 'error') errs.push(m.text()); });
    p.on('pageerror', e => errs.push(String(e)));
    p.on('response', r => { if (r.status() >= 400) bad.push(r.status() + ' ' + r.url()); });
    for (const path of paths) {
      errs.length = 0; bad.length = 0;
      await wait(+pause);
      const r = await p.goto(base + path, {waitUntil: 'networkidle'}).catch(() => null);
      const info = await p.evaluate(() => ({
        overflow: Math.max(0, document.documentElement.scrollWidth - document.documentElement.clientWidth),
        broken: [...document.images].filter(i => i.complete && i.naturalWidth === 0).map(i => i.src),
        noalt: [...document.images].filter(i => !i.hasAttribute('alt')).length,
        h1: document.querySelectorAll('h1').length,
        todo: document.body.innerText.match(/à mettre à jour|à rédiger|lorem ipsum|\[à compléter[^\]]*\]/gi) || [],
        links: [...document.querySelectorAll('a[href]')].map(a => a.href).filter(x => x.startsWith(location.origin)),
      })).catch(() => ({}));
      (info.links || []).forEach(l => seen.add(l.split('#')[0]));
      if (shots) await p.screenshot({path: `${shots}/${w}-${(path.replace(/\//g, '_') || '_')}.png`, fullPage: true});
      const row = {w, path, status: r ? r.status() : 'ERR', ...info, errs: [...errs], bad: [...bad]};
      delete row.links;
      out.push(row);
    }
  }
  const broken = [];
  const ctx = await b.newContext();
  for (const l of seen) {
    await wait(+pause);
    const r = await ctx.request.get(l, {maxRedirects: 5}).catch(() => null);
    if (!r || r.status() >= 400) broken.push((r ? r.status() : 'ERR') + ' ' + l);
  }
  fs.writeFileSync('crawl.json', JSON.stringify({out, broken, links: seen.size}, null, 1));
  const pb = out.filter(o => o.status !== 200 || o.overflow || (o.broken || []).length || o.errs.length || o.bad.length || o.h1 !== 1 || (o.todo || []).length);
  console.log(`${paths.length} pages × 3 largeurs ; pages à regarder : ${pb.length} ; liens internes testés : ${seen.size}, cassés : ${broken.length}`);
  pb.slice(0, 30).forEach(o => console.log(' ', o.w, o.path, 'statut', o.status, o.overflow ? `débordement +${o.overflow}px` : '', o.h1 !== 1 ? `h1=${o.h1}` : '', (o.broken || []).length ? `images cassées ${o.broken.length}` : '', o.errs.length ? `erreurs JS ${o.errs.length}` : '', (o.todo || []).join(', ')));
  broken.slice(0, 20).forEach(x => console.log('  lien cassé', x));
  await b.close();
})();
