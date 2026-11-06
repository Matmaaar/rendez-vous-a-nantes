/* Accueil : choix de la durée du parcours (maquette : boutons .seg). */
document.querySelectorAll('.parcours .seg button').forEach(function (b) {
  b.addEventListener('click', function () {
    var d = b.getAttribute('data-d');
    document.querySelectorAll('.parcours .seg button').forEach(function (x) {
      var on = x === b;
      x.classList.toggle('on', on);
      x.setAttribute('aria-pressed', on ? 'true' : 'false');
    });
    document.querySelectorAll('.parcours .plan').forEach(function (p) {
      p.hidden = p.getAttribute('data-plan') !== d;
    });
  });
});
