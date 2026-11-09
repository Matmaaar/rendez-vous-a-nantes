<?php
/**
 * Réglages du thème : supports, scripts, nettoyage.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'rdv-nantes', RDV_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'rdv-card', 800, 500, true );
} );

add_action( 'wp_enqueue_scripts', function () {
	// Inter (Google Fonts) seulement en secours, si les fichiers Suisse Int'l ne sont pas déposés.
	$deps = array();
	if ( ! rdv_font_faces() ) {
		wp_enqueue_style( 'rdv-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null );
		$deps[] = 'rdv-inter';
	}
	wp_enqueue_style( 'rdv-nantes', get_stylesheet_uri(), $deps, RDV_VERSION );
	if ( is_front_page() ) {
		wp_enqueue_script( 'rdv-nantes', RDV_URI . '/assets/js/rdv.js', array(), RDV_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
} );

add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = 'https://fonts.googleapis.com';
	}
	return $urls;
}, 10, 2 );

// Pas d'éditeur de blocs pour les contenus du site : l'éditeur classique suffit aux textes riches.
add_filter( 'use_block_editor_for_post_type', function ( $use, $post_type ) {
	return in_array( $post_type, array_merge( array( 'page' ), rdv_post_types() ), true ) ? false : $use;
}, 10, 2 );

// Les articles de blog ne sont pas utilisés : on les retire du menu d'admin.
add_action( 'admin_menu', function () {
	remove_menu_page( 'edit.php' );
} );

// Le séparateur du <title> de la maquette.
add_filter( 'document_title_separator', function () {
	return '—';
} );

/*
 * Sans Yoast SEO : on reprend quand même la meta description et le « noindex » importés
 * du site actuel (champs _yoast_wpseo_metadesc et _yoast_wpseo_meta-robots-noindex).
 */
add_action( 'wp_head', function () {
	if ( defined( 'WPSEO_VERSION' ) || ! is_singular() ) {
		return;
	}
	$desc = get_post_meta( get_queried_object_id(), '_yoast_wpseo_metadesc', true );
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
}, 1 );

add_filter( 'wp_robots', function ( $robots ) {
	if ( ! defined( 'WPSEO_VERSION' ) && is_singular() && '1' === get_post_meta( get_queried_object_id(), '_yoast_wpseo_meta-robots-noindex', true ) ) {
		$robots['noindex'] = true;
	}
	return $robots;
} );

/*
 * Suisse Int'l (police de la charte) : chargée depuis assets/fonts/ si les fichiers y sont déposés.
 * Ils ne sont pas versionnés (licence) ; à défaut, la pile de polices de style.css retombe sur Inter.
 */
function rdv_font_faces() {
	$faces = array(
		array( 'Regular', 400, 'normal' ),
		array( 'RegularItalic', 400, 'italic' ),
		array( 'Medium', 500, 'normal' ),
		array( 'SemiBold', 600, 'normal' ),
		array( 'Bold', 700, 'normal' ),
	);
	$css   = '';
	foreach ( $faces as $f ) {
		$file = '/assets/fonts/SuisseIntl-' . $f[0] . '.woff2';
		if ( file_exists( RDV_DIR . $file ) ) {
			$css .= "@font-face{font-family:\"Suisse Int'l\";src:url(" . esc_url( RDV_URI . $file ) . ") format(\"woff2\");font-weight:{$f[1]};font-style:{$f[2]};font-display:swap}";
		}
	}
	return $css;
}

add_action( 'wp_enqueue_scripts', function () {
	$css = rdv_font_faces();
	if ( $css ) {
		wp_add_inline_style( 'rdv-nantes', $css );
	}
}, 20 );
