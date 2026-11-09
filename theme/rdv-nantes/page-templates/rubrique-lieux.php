<?php
/**
 * Template Name: Rubrique — liste de fiches
 *
 * Maquette : catLieux() (Incontournables, Shopping, Pass Nantes).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$rdv_type  = rdv_meta( 'post_type' );
$rdv_items = $rdv_type ? rdv_items( $rdv_type ) : array();
rdv_crumb( array( array( get_the_title() ) ) );
rdv_head( rdv_meta( 'h1' ) ? rdv_meta( 'h1' ) : get_the_title(), rdv_meta( 'lead' ) );
?>
<section style="padding:0 0 48px"><div class="wrap">
  <?php
  rdv_mosaic( array_map( function ( $rdv_l ) {
	  return array( 'url' => get_permalink( $rdv_l ), 'title' => get_the_title( $rdv_l ), 'desc' => rdv_meta( 'subtitle', $rdv_l->ID ), 'post' => $rdv_l );
  }, $rdv_items ) );
  ?>
</div></section>
<?php if ( rdv_meta( 'encart_title' ) ) : ?>
<section style="padding:0 0 48px"><div class="wrap"><div class="encart jaune"><div class="txt"><h2><?php echo esc_html( rdv_meta( 'encart_title' ) ); ?></h2><p><?php echo wp_kses_post( rdv_resolve_links( rdv_meta( 'encart_text' ) ) ); ?></p></div><?php echo rdv_btn_field( rdv_meta( 'encart_btn' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div></div></section>
<?php endif; ?>
<?php if ( trim( get_the_content() ) ) : ?>
<section style="padding:0 0 48px"><div class="wrap"><div class="prose"><?php the_content(); ?></div></div></section>
<?php endif; ?>
<?php
rdv_others( get_the_ID() );
get_footer();
