<?php
/**
 * Template Name: Rubrique — page d'infos
 *
 * Maquette : catInfos() (Se déplacer, Aide).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
rdv_crumb( array( array( get_the_title() ) ) );
?>
<section style="padding:44px 0 72px"><div class="wrap qgrid">
  <div style="min-width:0">
    <h1 class="big" style="font-size:clamp(40px,5vw,60px);line-height:1.02"><?php echo esc_html( rdv_meta( 'h1' ) ? rdv_meta( 'h1' ) : get_the_title() ); ?></h1>
    <?php if ( rdv_meta( 'lead' ) ) : ?><p style="margin:18px 0 12px;font-size:21px;font-weight:500;max-width:640px"><?php echo esc_html( rdv_meta( 'lead' ) ); ?></p><?php endif; ?>
    <div class="prose"><?php the_content(); ?></div>
  </div>
  <?php rdv_aside( rdv_infos(), rdv_meta( 'maps' ) ? rdv_btn( rdv_meta( 'maps' ), __( 'Itinéraire', 'rdv-nantes' ), 'btn plein' ) : '' ); ?>
</div></section>
<?php
rdv_others( get_the_ID() );
get_footer();
