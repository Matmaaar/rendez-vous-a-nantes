<?php
/**
 * En-tête.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;
$rdv_active = rdv_current_rubrique_id();
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#app"><?php esc_html_e( 'Aller au contenu', 'rdv-nantes' ); ?></a>

<header>
  <div class="wrap hdr">
    <a class="brand" href="<?php echo esc_url( apply_filters( 'wpml_home_url', home_url( '/' ) ) ); ?>" aria-label="<?php esc_attr_e( 'Accueil Rendez-vous à Nantes', 'rdv-nantes' ); ?>">
      <svg width="26" height="34" viewBox="0 0 26 34" fill="none" stroke="#000" stroke-width="3" aria-hidden="true"><path d="M13 32C13 32 2 19.5 2 12.5C2 6.4 6.9 1.5 13 1.5S24 6.4 24 12.5C24 19.5 13 32 13 32Z"/></svg>
      <span><small><?php esc_html_e( 'Rendez-vous à', 'rdv-nantes' ); ?></small><b>Nantes</b></span>
    </a>
    <nav class="navbar" id="nav" aria-label="<?php esc_attr_e( 'Rubriques', 'rdv-nantes' ); ?>">
      <?php foreach ( rdv_rubriques( 'nav' ) as $rdv_r ) : ?>
        <a class="navlink<?php echo (int) $rdv_r->ID === (int) $rdv_active ? ' on' : ''; ?>" href="<?php echo esc_url( get_permalink( $rdv_r ) ); ?>"<?php echo (int) $rdv_r->ID === (int) $rdv_active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( get_the_title( $rdv_r ) ); ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="tools">
      <?php rdv_language_switcher(); ?>
      <a class="btn plein desk" href="<?php echo esc_url( rdv_link( '/24h-de-plus/pass-nantes/' ) ); ?>" style="padding:9px 18px;min-height:40px"><?php esc_html_e( 'Pass Nantes', 'rdv-nantes' ); ?></a>
    </div>
  </div>
</header>

<main id="app">
