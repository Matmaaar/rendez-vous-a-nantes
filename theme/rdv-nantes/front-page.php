<?php
/**
 * Accueil (maquette : home()).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
$rdv_plans = rdv_sections( rdv_meta( 'plans' ), '#' );
?>
<?php if ( rdv_meta( 'bonplan_text' ) ) : ?>
<section class="bonplan"><div class="wrap">
  <div class="l"><span class="pastille"><?php echo esc_html( rdv_meta( 'bonplan_label' ) ); ?></span><p><?php echo esc_html( rdv_meta( 'bonplan_text' ) ); ?></p></div>
  <?php echo rdv_btn_field( rdv_meta( 'bonplan_btn' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
</div></section>
<?php endif; ?>

<section class="hero"><div class="wrap">
  <h1 class="big"><?php echo wp_kses( rdv_meta( 'hero_title' ) ? rdv_meta( 'hero_title' ) : get_the_title(), array( 'br' => array() ) ); ?></h1>
  <?php if ( rdv_meta( 'hero_lead' ) ) : ?><p class="lead"><?php echo esc_html( rdv_meta( 'hero_lead' ) ); ?></p><?php endif; ?>
</div></section>

<section style="padding:8px 0 80px"><div class="wrap">
  <?php if ( rdv_meta( 'tiles_title' ) ) : ?><h2 class="h2"><?php echo esc_html( rdv_meta( 'tiles_title' ) ); ?></h2><?php endif; ?>
  <?php
  rdv_mosaic( array_map( function ( $rdv_r ) {
	  return array( 'url' => get_permalink( $rdv_r ), 'title' => get_the_title( $rdv_r ), 'desc' => rdv_meta( 'tile_desc', $rdv_r->ID ), 'post' => $rdv_r );
  }, rdv_rubriques( 'home' ) ), 3 );
  ?>
</div></section>

<?php if ( $rdv_plans ) : ?>
<section class="parcours"><div class="wrap">
  <h2 class="h2"><?php echo esc_html( rdv_meta( 'parcours_title' ) ); ?></h2>
  <p class="sub"><?php echo esc_html( rdv_meta( 'parcours_sub' ) ); ?></p>
  <div class="seg" role="group" aria-label="<?php esc_attr_e( 'Temps disponible', 'rdv-nantes' ); ?>">
    <?php foreach ( $rdv_plans as $rdv_i => $rdv_p ) : ?>
      <button type="button" data-d="<?php echo (int) $rdv_i; ?>" class="<?php echo 0 === $rdv_i ? 'on' : ''; ?>" aria-pressed="<?php echo 0 === $rdv_i ? 'true' : 'false'; ?>"><?php echo esc_html( $rdv_p['title'] ); ?></button>
    <?php endforeach; ?>
  </div>
  <?php foreach ( $rdv_plans as $rdv_i => $rdv_p ) : ?>
    <ol class="plan" data-plan="<?php echo (int) $rdv_i; ?>"<?php echo $rdv_i ? ' hidden' : ''; ?>>
      <?php foreach ( $rdv_p['lines'] as $rdv_l ) : ?>
        <li><a href="<?php echo esc_url( rdv_link( isset( $rdv_l[2] ) ? $rdv_l[2] : '' ) ); ?>"><?php echo esc_html( $rdv_l[0] ); ?><span><?php echo esc_html( isset( $rdv_l[1] ) ? $rdv_l[1] : '' ); ?></span></a></li>
      <?php endforeach; ?>
    </ol>
  <?php endforeach; ?>
  <?php if ( rdv_meta( 'plan_foot' ) ) : ?><p class="plan-foot"><?php echo wp_kses_post( rdv_resolve_links( rdv_meta( 'plan_foot' ) ) ); ?></p><?php endif; ?>
</div></section>
<?php endif; ?>
<?php
get_footer();
