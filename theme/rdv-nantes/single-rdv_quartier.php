<?php
/**
 * Fiche quartier (maquette : quartier()).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
list( $rdv_cat, $rdv_cat_url ) = rdv_rubrique_info( 'rdv_quartier' );
$rdv_items                     = rdv_items( 'rdv_quartier' );
list( $rdv_i, $rdv_n, $rdv_next ) = rdv_position( $rdv_items, get_the_ID() );

rdv_crumb( array( array( $rdv_cat, $rdv_cat_url ), array( get_the_title() ) ) );
?>
<section style="padding:44px 0 72px"><div class="wrap">
  <div class="fiche-head">
    <h1 class="big" style="font-size:clamp(40px,5vw,60px);line-height:1.02"><?php the_title(); ?></h1>
    <?php if ( rdv_meta( 'lead' ) ) : ?><p style="margin:22px 0 28px;font-size:19px;max-width:640px"><?php echo esc_html( rdv_meta( 'lead' ) ); ?></p><?php endif; ?>
  </div>
  <div class="qgrid">
  <div style="min-width:0">
    <?php rdv_fiche_photo(); ?>

    <?php $rdv_musts = rdv_rows( 'musts', array( 'title', 'desc', 'tag' ) ); ?>
    <?php if ( $rdv_musts ) : ?>
      <h2 class="label sec-title"><?php esc_html_e( 'Étapes incontournables', 'rdv-nantes' ); ?></h2>
      <div class="musts">
        <?php
        foreach ( $rdv_musts as $rdv_m ) :
			$rdv_m = array_pad( $rdv_m, 3, '' );
			?>
          <article class="must"><?php if ( $rdv_m[2] ) : ?><span class="maj" style="align-self:flex-start"><?php echo esc_html( $rdv_m[2] ); ?></span><?php endif; ?><h3><?php echo esc_html( $rdv_m[0] ); ?></h3><p><?php echo esc_html( $rdv_m[1] ); ?></p></article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <?php foreach ( rdv_bar_groups() as $rdv_g ) : ?>
      <h2 class="label sec-title line"><?php echo esc_html( $rdv_g['title'] ); ?></h2>
      <div>
        <?php
        foreach ( $rdv_g['lines'] as $rdv_b ) :
			list( $rdv_name, $rdv_addr, $rdv_tag, $rdv_desc ) = array_pad( $rdv_b, 4, '' );
			?>
          <details class="bar"><summary><?php echo esc_html( $rdv_name ); ?><?php if ( $rdv_addr ) : ?><span class="sa"><?php echo esc_html( $rdv_addr ); ?></span><?php endif; ?></summary>
            <div class="in">
              <?php if ( $rdv_tag ) : ?><p><span class="maj"><?php echo esc_html( $rdv_tag ); ?></span></p><?php endif; ?>
              <?php if ( $rdv_desc ) : ?><p class="desc"><?php echo esc_html( $rdv_desc ); ?></p><?php endif; ?>
              <?php if ( $rdv_addr ) : ?><p><?php echo RDV_ICON_PIN; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( $rdv_addr . ', Nantes' ); ?></p><?php endif; ?>
              <div class="btns" style="margin-top:6px"><?php echo rdv_btn( rdv_gmaps( trim( $rdv_name . ' ' . $rdv_addr ) . ' Nantes' ), __( 'Itinéraire', 'rdv-nantes' ), 'btn plein sm' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
            </div></details>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <?php if ( trim( get_the_content() ) ) : ?><div class="prose" style="margin-top:32px"><?php the_content(); ?></div><?php endif; ?>
    <?php if ( rdv_meta( 'note' ) ) : ?><p class="note"><?php echo esc_html( rdv_meta( 'note' ) ); ?></p><?php endif; ?>
  </div>
  <?php
  rdv_aside(
	  rdv_infos(),
	  rdv_meta( 'maps' ) ? rdv_btn( rdv_meta( 'maps' ), __( 'Itinéraire', 'rdv-nantes' ), 'btn plein' ) : ''
  );
  ?>
  </div>
</div></section>
<section class="pager"><div class="wrap">
  <a class="btn" href="<?php echo esc_url( $rdv_cat_url ); ?>">← <?php esc_html_e( 'Tous les quartiers', 'rdv-nantes' ); ?></a>
  <?php if ( $rdv_next && $rdv_n > 1 ) : ?>
    <a class="btn plein" href="<?php echo esc_url( get_permalink( $rdv_next ) ); ?>"><?php echo esc_html( ( $rdv_i + 1 === $rdv_n ) ? __( 'Revenir au début', 'rdv-nantes' ) : sprintf( /* translators: %s: ambiance du quartier suivant */ __( 'Suivant : %s', 'rdv-nantes' ), rdv_meta( 'amb', $rdv_next->ID ) ) ); ?> →</a>
  <?php endif; ?>
</div></section>
<?php
get_footer();
