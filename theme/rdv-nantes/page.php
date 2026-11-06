<?php
/**
 * Page simple (mentions légales, cookies…).
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
get_header();
the_post();
rdv_crumb( array( array( get_the_title() ) ) );
?>
<section class="page-simple"><div class="wrap">
  <h1 class="big"><?php the_title(); ?></h1>
  <div class="prose"><?php the_content(); ?></div>
</div></section>
<?php
get_footer();
