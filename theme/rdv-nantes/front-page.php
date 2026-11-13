<?php
/**
 * Accueil (maquette : home()).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
?>
<?php if ( rdv_meta( 'bonplan_text' ) ) : ?>
<section class="bonplan"><div class="wrap">
  <div class="l"><span class="pastille"><?php echo esc_html( rdv_meta( 'bonplan_label' ) ); ?></span><p><?php echo esc_html( rdv_meta( 'bonplan_text' ) ); ?></p></div>
  <?php echo rdv_btn_field( rdv_field( 'bonplan_btn' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div></section>
<?php endif; ?>

<section class="hero"><div class="wrap">
  <h1 class="big"><?php echo wp_kses( rdv_meta( 'hero_title' ) ? nl2br( rdv_meta( 'hero_title' ), false ) : get_the_title(), array( 'br' => array() ) ); ?></h1>
  <?php if ( rdv_meta( 'hero_lead' ) ) : ?><p class="lead"><?php echo esc_html( rdv_meta( 'hero_lead' ) ); ?></p><?php endif; ?>
</div></section>

<section style="padding:8px 0 80px"><div class="wrap">
  <?php if ( rdv_meta( 'tiles_title' ) ) : ?><h2 class="h2"><?php echo rdv_nbsp( esc_html( rdv_meta( 'tiles_title' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2><?php endif; ?>
  <?php
  rdv_mosaic( array_map( function ( $rdv_r ) {
	  return array( 'url' => get_permalink( $rdv_r ), 'title' => get_the_title( $rdv_r ), 'desc' => rdv_meta( 'tile_desc', $rdv_r->ID ), 'post' => $rdv_r );
  }, rdv_rubriques( 'home' ) ), 3 );
  ?>
</div></section>
<?php
get_footer();
