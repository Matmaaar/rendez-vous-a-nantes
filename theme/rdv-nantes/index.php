<?php
/**
 * Gabarit de secours.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>
<section class="page-simple"><div class="wrap">
  <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
      <article class="prose"><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></article>
    <?php endwhile; ?>
  <?php else : ?>
    <p><?php esc_html_e( 'Aucun contenu.', 'rdv-nantes' ); ?></p>
  <?php endif; ?>
</div></section>
<?php
get_footer();
