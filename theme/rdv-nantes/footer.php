<?php
/**
 * Pied de page.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer>
  <div class="wrap">
    <div class="foot">
      <div>
        <p class="t"><?php esc_html_e( 'L’Accueil du Voyage à Nantes', 'rdv-nantes' ); ?></p>
        <p><?php esc_html_e( '9 rue des États, face au Château des ducs de Bretagne', 'rdv-nantes' ); ?><br><?php esc_html_e( 'Du lundi au samedi : 10 h – 18 h (jeudi à partir de 11 h)', 'rdv-nantes' ); ?><br><?php esc_html_e( 'Dimanche et jours fériés : 10 h – 17 h', 'rdv-nantes' ); ?></p>
      </div>
      <div>
        <p class="t"><?php esc_html_e( 'Par téléphone', 'rdv-nantes' ); ?></p>
        <a class="tel" href="tel:+33892464044" style="text-decoration:none"><b>0 892 464 044</b><small><?php esc_html_e( 'Service 0,35 € / min', 'rdv-nantes' ); ?><br><?php esc_html_e( '+ prix appel', 'rdv-nantes' ); ?></small></a>
        <p style="margin-top:12px"><?php esc_html_e( 'Par mail :', 'rdv-nantes' ); ?> <a href="mailto:info@levoyageanantes.fr">info@levoyageanantes.fr</a></p>
      </div>
      <div>
        <p class="t"><?php esc_html_e( 'Un site du Voyage à Nantes', 'rdv-nantes' ); ?></p>
        <p><?php esc_html_e( 'Actus, infos, bons plans :', 'rdv-nantes' ); ?> <a href="<?php echo esc_url( __( 'https://levoyageanantes.fr', 'rdv-nantes' ) ); ?>" target="_blank" rel="noopener">levoyageanantes.fr</a></p>
        <p style="margin-top:12px"><a href="<?php echo esc_url( rdv_link( '/aide/' ) ); ?>"><?php esc_html_e( 'Besoin d’aide ?', 'rdv-nantes' ); ?></a></p>
      </div>
    </div>
    <div class="legal">
      <a href="<?php echo esc_url( rdv_link( '/mentions-legales/' ) ); ?>"><?php esc_html_e( 'Mentions légales', 'rdv-nantes' ); ?></a>
      <?php if ( rdv_get_by_slug( 'accessibilite', 'page' ) ) : ?>
        <a href="<?php echo esc_url( rdv_link( '/accessibilite/' ) ); ?>"><?php esc_html_e( 'Accessibilité', 'rdv-nantes' ); ?></a>
      <?php endif; ?>
      <a href="<?php echo esc_url( rdv_link( '/cookiespolicy/' ) ); ?>"><?php esc_html_e( 'Cookies', 'rdv-nantes' ); ?></a>
    </div>
  </div>
</footer>

<?php
$rdv_active = rdv_current_rubrique_id();
$rdv_icon   = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>';
?>
<button class="mfab" type="button" aria-expanded="false" aria-controls="rdv-menu" data-close-label="<?php esc_attr_e( 'Fermer', 'rdv-nantes' ); ?>" data-menu-label="<?php esc_attr_e( 'Menu', 'rdv-nantes' ); ?>"><span class="ic"><?php echo $rdv_icon; // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span class="lab"><?php esc_html_e( 'Menu', 'rdv-nantes' ); ?></span></button>
<div class="mscrim" hidden></div>
<div id="rdv-menu" class="msheet" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'rdv-nantes' ); ?>" hidden>
  <nav aria-label="<?php esc_attr_e( 'Rubriques', 'rdv-nantes' ); ?>">
    <ul>
      <?php foreach ( rdv_rubriques( 'nav' ) as $rdv_r ) : ?>
        <li><a href="<?php echo esc_url( get_permalink( $rdv_r ) ); ?>"<?php echo (int) $rdv_r->ID === (int) $rdv_active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( get_the_title( $rdv_r ) ); ?><span aria-hidden="true">→</span></a></li>
      <?php endforeach; ?>
    </ul>
    <a class="btn plein pass" href="<?php echo esc_url( rdv_link( '/24h-de-plus/pass-nantes/' ) ); ?>"><?php esc_html_e( 'Pass Nantes', 'rdv-nantes' ); ?></a>
  </nav>
</div>

<?php wp_footer(); ?>
</body>
</html>
