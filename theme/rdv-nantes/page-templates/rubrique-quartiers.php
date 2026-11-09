<?php
/**
 * Template Name: Rubrique — quartiers
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
rdv_head( rdv_meta( 'h1' ) ? rdv_meta( 'h1' ) : get_the_title(), rdv_meta( 'lead' ) );
?>
<section style="padding:0 0 72px"><div class="wrap">
  <?php
  rdv_mosaic( array_map( function ( $rdv_q ) {
	  return array(
		  'url'   => get_permalink( $rdv_q ),
		  'title' => rdv_meta( 'amb', $rdv_q->ID ),
		  'desc'  => rdv_meta( 'where', $rdv_q->ID ),
		  'post'  => $rdv_q,
		  'tags'  => wp_list_pluck( rdv_lines( rdv_meta( 'badges', $rdv_q->ID ) ), 0 ),
	  );
  }, $rdv_items ), count( $rdv_items ) === 4 ? 4 : 0 );
  ?>
</div></section>
<?php
rdv_others( get_the_ID() );
get_footer();
