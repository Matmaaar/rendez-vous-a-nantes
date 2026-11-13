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
rdv_head( rdv_meta( 'h1' ) ? rdv_meta( 'h1' ) : get_the_title(), rdv_meta( 'lead' ) );
$rdv_cta    = rdv_btn_field( rdv_field( 'cta' ) );
$rdv_rows   = rdv_rows( 'items', array( 'title', 'precision', 'desc', 'link', 'photo' ) );
$rdv_label  = rdv_meta( 'link_label' ) ? rdv_meta( 'link_label' ) : __( 'Voir', 'rdv-nantes' );
$rdv_photos = (bool) array_filter( wp_list_pluck( $rdv_rows, 4 ) ); // liste illustrée si au moins une photo
?>
<section style="padding:0 0 64px"><div class="wrap" style="max-width:<?php echo $rdv_photos ? '1240px' : '960px'; ?>">
  <?php if ( $rdv_cta ) : ?><p style="text-align:center;margin:-8px 0 40px"><?php echo $rdv_cta; // phpcs:ignore WordPress.Security.EscapeOutput ?></p><?php endif; ?>
  <?php if ( rdv_meta( 'list_title' ) ) : ?><h2 class="label sec-title line" style="margin-top:0"><?php echo esc_html( rdv_meta( 'list_title' ) ); ?></h2><?php endif; ?>
  <?php if ( $rdv_photos ) : // Liste illustrée : photo, nom, description, bouton. ?>
  <div class="rlist">
    <?php foreach ( $rdv_rows as $rdv_it ) : ?>
      <article class="rrow">
        <?php if ( $rdv_it[4] && is_numeric( $rdv_it[4] ) ) : ?>
          <a class="rph" href="<?php echo esc_url( rdv_link( $rdv_it[3] ) ); ?>" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true"><?php echo wp_get_attachment_image( (int) $rdv_it[4], 'large', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width:640px) 92vw, 600px' ) ); ?></a>
        <?php elseif ( $rdv_it[4] ) : ?>
          <a class="rph" href="<?php echo esc_url( rdv_link( $rdv_it[3] ) ); ?>" target="_blank" rel="noopener" tabindex="-1" aria-hidden="true"><img src="<?php echo esc_url( $rdv_it[4] ); ?>" alt="" loading="lazy"></a>
        <?php else : ?>
          <span class="rph" aria-hidden="true"></span>
        <?php endif; ?>
        <div class="rtx">
          <h3><?php echo esc_html( $rdv_it[0] ); ?></h3>
          <?php if ( $rdv_it[1] ) : ?><p class="meta"><?php echo esc_html( $rdv_it[1] ); ?></p><?php endif; ?>
          <?php if ( $rdv_it[2] ) : ?><p><?php echo esc_html( $rdv_it[2] ); ?></p><?php endif; ?>
          <?php echo $rdv_it[3] ? rdv_btn( $rdv_it[3], $rdv_label, 'btn sm' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <?php else : ?>
  <div class="liste" style="border-top:0">
    <?php foreach ( $rdv_rows as $rdv_it ) : ?>
      <div class="lrow"><div><h3><?php echo esc_html( $rdv_it[0] ); ?></h3><?php if ( $rdv_it[1] ) : ?><p class="meta"><?php echo esc_html( $rdv_it[1] ); ?></p><?php endif; ?><?php if ( $rdv_it[2] ) : ?><p><?php echo esc_html( $rdv_it[2] ); ?></p><?php endif; ?></div><?php echo $rdv_it[3] ? rdv_btn( $rdv_it[3], $rdv_label, 'btn sm' ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <?php if ( trim( get_the_content() ) ) : ?><div class="prose" style="margin-top:40px"><?php the_content(); ?></div><?php endif; ?>
  <?php if ( rdv_meta( 'after_title' ) ) : ?>
    <div class="encart" style="margin-top:40px"><div class="txt"><h2><?php echo esc_html( rdv_meta( 'after_title' ) ); ?></h2><p><?php echo wp_kses_post( rdv_resolve_links( rdv_meta( 'after_text' ) ) ); ?></p></div><div class="btns"><?php echo rdv_btn_field( rdv_field( 'after_btn1' ) ) . rdv_btn_field( rdv_field( 'after_btn2' ), 'btn' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div></div>
  <?php endif; ?>
</div></section>
<?php
rdv_others( get_the_ID() );
get_footer();
