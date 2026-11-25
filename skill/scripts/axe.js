// Audit axe-core WCAG 2.1 AA : node axe.js <url-de-base> <fichier-de-chemins>
// Prérequis : npm i axe-core (dans le dossier courant) ; Playwright installé.
// Teste chaque chemin à 1280 et 390 px, regroupe les infractions (VIOL) et les points à vérifier (TODO).
const {chromium} = require('playwright');
const fs = require('fs');
const [base, file] = process.argv.slice(2);
if (!base || !file) { console.error('usage: node axe.js <url-de-base> <fichier-de-chemins>'); process.exit(1); }
const AXE = fs.readFileSync(require.resolve('axe-core/axe.min.js', {paths: [process.cwd()]}), 'utf8');
const paths = fs.readFileSync(file, 'utf8').trim().split('\n').map(s => s.trim()).filter(Boolean);

(async () => {
  const b = await chromium.launch();
  const agg = {};
  const add = (k, v, page, nodes) => {
    agg[k] = agg[k] || {impact: v.impact, help: v.help, pages: new Set(), nodes: []};
    agg[k].pages.add(page);
    nodes.forEach(n => { if (agg[k].nodes.length < 6) agg[k].nodes.push(n); });
  };
  for (const w of [1280, 390]) {
    const p = await b.newPage({viewport: {width: w, height: 900}});
    for (const path of paths) {
      await p.goto(base + path, {waitUntil: 'networkidle'});
      await p.addScriptTag({content: AXE});
      const r = await p.evaluate(() => axe.run(document, {runOnly: {type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa']}, resultTypes: ['violations', 'incomplete']}));
      r.violations.forEach(v => add('VIOL ' + v.id, v, w + ' ' + path, v.nodes.slice(0, 3).map(n => (n.target || []).join(' ') + ' | ' + (n.failureSummary || '').split('\n')[1])));
      r.incomplete.forEach(v => add('TODO ' + v.id, v, w + ' ' + path, v.nodes.slice(0, 2).map(n => (n.target || []).join(' '))));
    }
  }
  const viol = Object.keys(agg).filter(k => k.startsWith('VIOL')).length;
  console.log(`${paths.length} pages × 2 largeurs ; types d'infractions : ${viol}`);
  for (const [k, v] of Object.entries(agg)) {
    console.log(`\n${k} [${v.impact}] ${v.help} — ${v.pages.size} page(s)`);
    [...v.pages].slice(0, 4).forEach(x => console.log('   ', x));
    v.nodes.forEach(n => console.log('   >', n));
  }
  await b.close();
})();
