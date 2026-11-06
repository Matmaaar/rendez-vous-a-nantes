<?php
/**
 * Types de contenu.
 *
 * Chaque rubrique « liste de fiches » est une page (gabarit de rubrique) dont le slug
 * est identique à la base d'URL du type de contenu : /incontournable/ est la page de
 * rubrique, /incontournable/le-musee-darts/ une fiche. Les URL du site actuel sont ainsi conservées.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Définition des types : clé => [slug d'URL, libellé pluriel, libellé singulier, icône].
 *
 * @return array
 */
function rdv_post_type_defs() {
	return array(
		'rdv_quartier'       => array( 'prendre-un-verre', __( 'Prendre un verre', 'rdv-nantes' ), __( 'Quartier', 'rdv-nantes' ), 'dashicons-location-alt' ),
		'rdv_incontournable' => array( 'incontournable', __( 'Incontournables', 'rdv-nantes' ), __( 'Incontournable', 'rdv-nantes' ), 'dashicons-star-filled' ),
		'rdv_shopping'       => array( 'shopping', __( 'Shopping', 'rdv-nantes' ), __( 'Adresse shopping', 'rdv-nantes' ), 'dashicons-cart' ),
		'rdv_pass'           => array( '24h-de-plus', __( 'Pass Nantes', 'rdv-nantes' ), __( 'Fiche Pass Nantes', 'rdv-nantes' ), 'dashicons-tickets-alt' ),
	);
}

/**
 * Clés des types de contenu du thème.
 *
 * @return string[]
 */
function rdv_post_types() {
	return array_keys( rdv_post_type_defs() );
}

add_action( 'init', function () {
	foreach ( rdv_post_type_defs() as $type => $def ) {
		list( $slug, $plural, $singular, $icon ) = $def;
		register_post_type( $type, array(
			'labels'       => array(
				'name'          => $plural,
				'singular_name' => $singular,
				'add_new_item'  => sprintf( /* translators: %s: libellé */ __( 'Ajouter : %s', 'rdv-nantes' ), $singular ),
				'edit_item'     => sprintf( /* translators: %s: libellé */ __( 'Modifier : %s', 'rdv-nantes' ), $singular ),
				'all_items'     => $plural,
			),
			'public'       => true,
			'has_archive'  => false, // La page de rubrique fait office de liste.
			'hierarchical' => false,
			'menu_icon'    => $icon,
			'menu_position'=> 20,
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'revisions' ),
			'rewrite'      => array( 'slug' => $slug, 'with_front' => false ),
		) );
	}
} );

// Ordre manuel (champ « Ordre ») dans les listes d'admin.
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() && $q->is_main_query() && in_array( $q->get( 'post_type' ), rdv_post_types(), true ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', 'menu_order title' );
		$q->set( 'order', 'ASC' );
	}
} );

/**
 * Fiches d'un type, dans l'ordre du site (langue courante sous WPML).
 *
 * @param string $type Type de contenu.
 * @return WP_Post[]
 */
function rdv_items( $type ) {
	return get_posts( array(
		'post_type'        => $type,
		'posts_per_page'   => -1,
		'orderby'          => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'suppress_filters' => false,
	) );
}
