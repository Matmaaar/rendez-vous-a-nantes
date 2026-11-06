<?php
/**
 * Fiche lieu : incontournable, shopping, Pass Nantes (maquette : lieu()).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$rdv_type = get_post_type();
list( $rdv_cat, $rdv_cat_url ) = rdv_rubrique_info( $rdv_type );
list( $rdv_i, $rdv_n, $rdv_next ) = rdv_position( rdv_items( $rdv_type ), get_the_ID() );

$rdv_btns = '';
if ( rdv_meta( 'maps' ) ) {
	$rdv_btns .= rdv_btn( rdv_meta( 'maps' ), __( 'Itinéraire', 'rdv-nantes' ), 'btn plein' );
}
if ( rdv_meta( 'buy' ) ) {
	$rdv_btns .= rdv_btn( rdv_meta( 'buy' ), rdv_meta( 'buy_label' ) ? rdv_meta( 'buy_label' ) : __( 'Acheter en ligne', 'rdv-nantes' ), 'btn plein' );
}
if ( rdv_meta( 'more' ) ) {
	$rdv_btns .= rdv_btn( rdv_meta( 'more' ), __( 'En savoir plus', 'rdv-nantes' ), 'btn' );
}
$rdv_link = array_map( 'trim', explode( '|', rdv_meta( 'link' ) ) );

rdv_crumb( array( array( $rdv_cat, $rdv_cat_url ), array( get_the_title() ) ) );
?>
<section style="padding:44px 0 72px"><div class="wrap qgrid">
  <div style="min-width:0">
    <p class="label" style="margin-bottom:16px"><?php echo esc_html( sprintf( /* translators: 1: rubrique, 2: position, 3: total */ __( '%1$s · %2$d sur %3$d', 'rdv-nantes' ), $rdv_cat, $rdv_i + 1, $rdv_n ) ); ?></p>
    <h1 class="big" style="font-size:clamp(40px,5vw,60px);line-height:1.02"><?php the_title(); ?></h1>
    <?php if ( rdv_meta( 'subtitle' ) ) : ?><p style="margin:18px 0 28px;font-size:21px;font-weight:500;max-width:640px"><?php echo esc_html( rdv_meta( 'subtitle' ) ); ?></p><?php endif; ?>
    <div class="prose"><?php the_content(); ?></div>
    <?php if ( count( $rdv_link ) >= 2 && $rdv_link[0] ) : ?><p style="margin-top:24px"><?php echo rdv_btn( $rdv_link[1], $rdv_link[0] . ' →', 'btn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
  </div>
  <?php rdv_aside( rdv_meta( 'infos' ), $rdv_btns ); ?>
</div></section>
<section class="pager"><div class="wrap">
  <a class="btn" href="<?php echo esc_url( $rdv_cat_url ); ?>">← <?php echo esc_html( $rdv_cat ); ?></a>
  <?php if ( $rdv_next && $rdv_n > 1 ) : ?>
    <a class="btn plein" href="<?php echo esc_url( get_permalink( $rdv_next ) ); ?>"><?php echo esc_html( sprintf( /* translators: %s: fiche suivante */ __( 'Suivant : %s', 'rdv-nantes' ), get_the_title( $rdv_next ) ) ); ?> →</a>
  <?php endif; ?>
</div></section>
<?php
get_footer();
