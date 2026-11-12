/* Menu mobile : pastille « Menu » en bas à droite, panneau qui monte du bas. */
(function () {
  var btn = document.querySelector('.mfab'), sheet = document.getElementById('rdv-menu'), scrim = document.querySelector('.mscrim');
  if (!btn || !sheet) return;
  var lab = btn.querySelector('.lab'), ic = btn.querySelector('.ic'), menuIcon = ic.innerHTML,
      closeIcon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>';
  function toggle(open) {
    sheet.hidden = !open; scrim.hidden = !open;
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    lab.textContent = open ? btn.dataset.closeLabel : btn.dataset.menuLabel;
    ic.innerHTML = open ? closeIcon : menuIcon;
    document.body.style.overflow = open ? 'hidden' : '';
    (open ? sheet.querySelector('a') : btn).focus();
  }
  btn.addEventListener('click', function () { toggle(sheet.hidden); });
  scrim.addEventListener('click', function () { toggle(false); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !sheet.hidden) toggle(false); });
})();
