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

<?php wp_footer(); ?>
</body>
</html>
