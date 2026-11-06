<?php
/**
 * Template Name: Rubrique — liste d'adresses
 *
 * Maquette : catListe() (Manger, Footing).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
rdv_crumb( array( array( get_the_title() ) ) );
rdv_head( rdv_rubrique_label( get_the_ID() ), rdv_meta( 'h1' ) ? rdv_meta( 'h1' ) : get_the_title(), rdv_meta( 'lead' ) );
$rdv_cta = rdv_btn_field( rdv_meta( 'cta' ) );
?>
<section style="padding:0 0 64px"><div class="wrap" style="max-width:960px">
  <?php if ( $rdv_cta ) : ?><p style="text-align:center;margin:-8px 0 40px"><?php echo $rdv_cta; // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
  <?php if ( rdv_meta( 'list_title' ) ) : ?><h2 class="label sec-title line" style="margin-top:0"><?php echo esc_html( rdv_meta( 'list_title' ) ); ?></h2><?php endif; ?>
  <div class="liste" style="border-top:0">
    <?php
    foreach ( rdv_lines( rdv_meta( 'items' ) ) as $rdv_it ) :
		$rdv_it = array_pad( $rdv_it, 4, '' );
		?>
      <div class="lrow"><div><h3><?php echo esc_html( $rdv_it[0] ); ?></h3><?php if ( $rdv_it[1] ) : ?><p class="meta"><?php echo esc_html( $rdv_it[1] ); ?></p><?php endif; ?><?php if ( $rdv_it[2] ) : ?><p><?php echo esc_html( $rdv_it[2] ); ?></p><?php endif; ?></div><?php echo $rdv_it[3] ? rdv_btn( $rdv_it[3], rdv_meta( 'link_label' ) ? rdv_meta( 'link_label' ) : __( 'Voir', 'rdv-nantes' ), 'btn sm' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
    <?php endforeach; ?>
  </div>
  <?php if ( trim( get_the_content() ) ) : ?><div class="prose" style="margin-top:40px"><?php the_content(); ?></div><?php endif; ?>
  <?php if ( rdv_meta( 'after_title' ) ) : ?>
    <div class="encart" style="margin-top:40px"><div class="txt"><h2><?php echo esc_html( rdv_meta( 'after_title' ) ); ?></h2><p><?php echo wp_kses_post( rdv_resolve_links( rdv_meta( 'after_text' ) ) ); ?></p></div><div class="btns"><?php echo rdv_btn_field( rdv_meta( 'after_btn1' ) ) . rdv_btn_field( rdv_meta( 'after_btn2' ), 'btn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div></div>
  <?php endif; ?>
</div></section>
<?php
rdv_others( get_the_ID() );
get_footer();
