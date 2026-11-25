// Prépare la mesure du contraste des textes posés sur des photos.
// node tiles.js <url-de-base> <fichier-de-chemins> <selecteur-case> <selecteurs-textes> <dossier-sortie>
// ex. : node tiles.js http://localhost:8080 pages.txt ".mtile" ".t,.d" ./tiles
// Pour chaque case : capture avec les textes rendus transparents (le halo / dégradé reste), position et
// taille/graisse de chaque texte. Écrit <dossier-sortie>/tiles.json ; lancer ensuite contrast.py.
const {chromium} = require('playwright');
const fs = require('fs');
const [base, file, tileSel, textSels, outDir] = process.argv.slice(2);
if (!outDir) { console.error('usage: node tiles.js <url-de-base> <fichier-de-chemins> <selecteur-case> <selecteurs-textes> <dossier-sortie>'); process.exit(1); }
const paths = fs.readFileSync(file, 'utf8').trim().split('\n').map(s => s.trim()).filter(Boolean);
const texts = textSels.split(',').map(s => s.trim());
const HIDE = texts.map(t => `html.hidetext ${tileSel} ${t}`).join(',') + '{color:transparent!important;text-shadow:none!important;border-color:transparent!important}';

(async () => {
  fs.mkdirSync(outDir, {recursive: true});
  const b = await chromium.launch();
  const out = [];
  for (const w of [1280, 390]) {
    const p = await b.newPage({viewport: {width: w, height: 900}});
    for (const path of paths) {
      await p.goto(base + path, {waitUntil: 'networkidle'});
      await p.addStyleTag({content: HIDE});
      await p.evaluate(async sel => {
        for (const i of document.querySelectorAll(sel + ' img')) {
          i.loading = 'eager';
          if (!i.complete) await new Promise(r => { i.onload = i.onerror = r; });
        }
      }, tileSel);
      const tiles = await p.$$(tileSel);
      for (let i = 0; i < tiles.length; i++) {
        await tiles[i].scrollIntoViewIfNeeded();
        const info = await tiles[i].evaluate((el, texts) => {
          const box = el.getBoundingClientRect();
          return texts.map(sel => {
            const e = el.querySelector(sel);
            if (!e) return null;
            const r = document.createRange();
            r.selectNodeContents(e);
            const cs = getComputedStyle(e);
            return {sel, text: e.textContent.trim().slice(0, 60), font: cs.fontSize + ' ' + cs.fontWeight,
              rects: [...r.getClientRects()].map(c => [c.left - box.left, c.top - box.top, c.width, c.height])};
          }).filter(Boolean);
        }, texts);
        await p.evaluate(() => document.documentElement.classList.toggle('hidetext', true));
        const f = `${w}-${path.replace(/\//g, '_')}-${i}.png`;
        await tiles[i].screenshot({path: `${outDir}/${f}`});
        await p.evaluate(() => document.documentElement.classList.toggle('hidetext', false));
        out.push({w, path, i, f, texts: info});
      }
    }
  }
  fs.writeFileSync(`${outDir}/tiles.json`, JSON.stringify(out));
  console.log(out.length, 'cases capturées dans', outDir);
  await b.close();
})();
