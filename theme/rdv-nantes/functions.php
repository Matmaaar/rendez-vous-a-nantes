<?php
/**
 * Thème Rendez-vous à Nantes.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;

define( 'RDV_VERSION', '1.0.0' );
define( 'RDV_DIR', get_template_directory() );
define( 'RDV_URI', get_template_directory_uri() );

require RDV_DIR . '/inc/setup.php';
require RDV_DIR . '/inc/post-types.php';
require RDV_DIR . '/inc/fields.php';
require RDV_DIR . '/inc/template-tags.php';
require RDV_DIR . '/inc/import.php';
