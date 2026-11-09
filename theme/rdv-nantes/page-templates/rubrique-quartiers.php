<?php
/**
 * Template Name: Rubrique — quartiers avec carte
 *
 * Maquette : catQuartiers() (Prendre un verre).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$rdv_items = rdv_items( 'rdv_quartier' );
rdv_crumb( array( array( get_the_title() ) ) );
rdv_head( rdv_rubrique_label( get_the_ID() ), rdv_meta( 'h1' ) ? rdv_meta( 'h1' ) : get_the_title(), rdv_meta( 'lead' ) );
?>
<section style="padding:0 0 40px"><div class="wrap">
  <div class="map">
    <img src="<?php echo esc_url( RDV_URI . '/assets/img/plan-nantes.webp' ); ?>" width="1840" height="1600" alt="<?php esc_attr_e( 'Plan du centre de Nantes et de l’Île de Nantes', 'rdv-nantes' ); ?>">
    <span class="tag" style="left:79%;top:41%"><?php esc_html_e( 'La Loire', 'rdv-nantes' ); ?></span>
    <span class="tag soft" style="left:40%;top:72%;font-size:14px"><?php esc_html_e( 'Île de Nantes', 'rdv-nantes' ); ?></span>
    <span class="tag soft" style="left:51%;top:27%;text-transform:none;letter-spacing:0;font-weight:600"><?php esc_html_e( 'Château', 'rdv-nantes' ); ?></span>
    <span class="cite" style="left:62%;top:37%"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" aria-hidden="true"><path d="M12 21s-7-7.4-7-12a7 7 0 0 1 14 0c0 4.6-7 12-7 12z"/></svg><?php esc_html_e( 'Cité des Congrès', 'rdv-nantes' ); ?></span>
    <?php
    foreach ( $rdv_items as $rdv_q ) :
		$rdv_x = rdv_meta( 'map_x', $rdv_q->ID );
		$rdv_y = rdv_meta( 'map_y', $rdv_q->ID );
		if ( ! $rdv_x || ! $rdv_y ) {
			continue;
		}
		$rdv_where = rdv_meta( 'where', $rdv_q->ID );
		$rdv_short = trim( explode( '·', $rdv_where )[0] );
		?>
      <a class="pin" href="<?php echo esc_url( get_permalink( $rdv_q ) ); ?>" style="left:<?php echo esc_attr( $rdv_x ); ?>;top:<?php echo esc_attr( $rdv_y ); ?>" aria-label="<?php echo esc_attr( rdv_meta( 'amb', $rdv_q->ID ) . ' — ' . $rdv_where ); ?>"><span class="pin-n"><?php echo esc_html( rdv_meta( 'num', $rdv_q->ID ) ); ?></span><span class="pin-l"><?php echo esc_html( $rdv_short ); ?></span></a>
    <?php endforeach; ?>
  </div>
  <p class="credit"><?php esc_html_e( '© OpenStreetMap contributors · Les guinguettes sont réparties en ville et aux environs.', 'rdv-nantes' ); ?></p>
</div></section>
<section style="padding:0 0 72px"><div class="wrap">
  <?php
  rdv_mosaic( array_map( function ( $rdv_q ) {
	  return array(
		  'url'   => get_permalink( $rdv_q ),
		  'title' => rdv_meta( 'amb', $rdv_q->ID ),
		  'desc'  => rdv_meta( 'where', $rdv_q->ID ),
		  'post'  => $rdv_q,
		  'num'   => rdv_meta( 'num', $rdv_q->ID ), // renvoie au repère de la carte
		  'tags'  => wp_list_pluck( rdv_lines( rdv_meta( 'badges', $rdv_q->ID ) ), 0 ),
	  );
  }, $rdv_items ), count( $rdv_items ) === 4 ? 4 : 0 );
  ?>
</div></section>
<?php
rdv_others( get_the_ID() );
get_footer();
