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
rdv_head( rdv_rubrique_label( get_the_ID() ), rdv_meta( 'h1' ) ? rdv_meta( 'h1' ) : get_the_title(), rdv_meta( 'lead' ) );
?>
<section style="padding:0 0 48px"><div class="wrap"><div class="grid3">
  <?php foreach ( $rdv_items as $rdv_i => $rdv_l ) : ?>
    <a class="tile" href="<?php echo esc_url( get_permalink( $rdv_l ) ); ?>"><span class="n"><?php echo esc_html( str_pad( (string) ( $rdv_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><span style="flex:1"><span class="t"><?php echo esc_html( get_the_title( $rdv_l ) ); ?></span><span class="d"><?php echo esc_html( rdv_meta( 'subtitle', $rdv_l->ID ) ); ?></span></span><span class="fl" aria-hidden="true">→</span></a>
  <?php endforeach; ?>
</div></div></section>
<?php if ( rdv_meta( 'encart_title' ) ) : ?>
<section style="padding:0 0 48px"><div class="wrap"><div class="encart jaune"><div class="txt"><h2><?php echo esc_html( rdv_meta( 'encart_title' ) ); ?></h2><p><?php echo wp_kses_post( rdv_resolve_links( rdv_meta( 'encart_text' ) ) ); ?></p></div><?php echo rdv_btn_field( rdv_meta( 'encart_btn' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div></div></section>
<?php endif; ?>
<?php if ( trim( get_the_content() ) ) : ?>
<section style="padding:0 0 48px"><div class="wrap"><div class="prose"><?php the_content(); ?></div></div></section>
<?php endif; ?>
<?php
rdv_others( get_the_ID() );
get_footer();
