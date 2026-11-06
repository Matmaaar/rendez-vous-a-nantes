<?php
/**
 * Page introuvable.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="e404"><div class="wrap">
  <p class="label" style="margin-bottom:18px">404</p>
  <h1 class="big"><?php esc_html_e( 'Page introuvable', 'rdv-nantes' ); ?></h1>
  <p class="lead"><?php esc_html_e( 'Cette page n’existe pas ou plus. Repartez de l’accueil ou choisissez une rubrique.', 'rdv-nantes' ); ?></p>
  <div class="btns"><a class="btn plein" href="<?php echo esc_url( apply_filters( 'wpml_home_url', home_url( '/' ) ) ); ?>"><?php esc_html_e( 'Retour à l’accueil', 'rdv-nantes' ); ?></a></div>
</div></section>
<?php
rdv_others( 0 );
get_footer();
