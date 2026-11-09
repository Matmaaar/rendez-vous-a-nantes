<?php
/**
 * Import du contenu initial (import/content.json) : FR, puis EN / ES reliés sous WPML.
 *
 * - En ligne de commande : wp rdv import [--no-images]
 * - Dans l'admin : Outils → Import Rendez-vous à Nantes
 *
 * L'import est rejouable : un contenu déjà importé (même clé, même langue) est mis à jour.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lance l'import.
 *
 * @param bool $with_images Importer les images à la une.
 * @return string[] Journal.
 */
function rdv_import( $with_images = true ) {
	$file = RDV_DIR . '/import/content.json';
	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( ! $data ) {
		return array( 'Fichier import/content.json illisible.' );
	}
	$log     = array();
	$wpml    = rdv_wpml_active();
	$default = $wpml ? apply_filters( 'wpml_default_language', 'fr' ) : 'fr';
	$active  = $wpml ? array_keys( (array) apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) ) ) : array( 'fr' );
	if ( ! $wpml ) {
		$log[] = 'WPML inactif : seul le contenu français est importé.';
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = 'Permaliens réglés sur /%postname%/.';
	}

	$ids = array();
	foreach ( $data['items'] as $item ) {
		$fr_id = 0;
		foreach ( array( 'fr', 'en', 'es' ) as $lang ) {
			if ( empty( $item['langs'][ $lang ] ) || ( 'fr' !== $lang && ( ! $wpml || ! in_array( $lang, $active, true ) ) ) ) {
				continue;
			}
			if ( $wpml ) {
				do_action( 'wpml_switch_language', $lang );
			}
			$l  = $item['langs'][ $lang ];
			$id = rdv_import_post( $item, $lang, $l );
			if ( is_wp_error( $id ) ) {
				$log[] = sprintf( 'Erreur %s (%s) : %s', $item['key'], $lang, $id->get_error_message() );
				continue;
			}
			if ( $wpml ) {
				$type = 'post_' . $item['type'];
				if ( 'fr' === $lang ) {
					do_action( 'wpml_set_element_language_details', array( 'element_id' => $id, 'element_type' => $type, 'trid' => false, 'language_code' => $default ) );
					$fr_id = $id;
				} elseif ( $fr_id ) {
					$trid = apply_filters( 'wpml_element_trid', null, $fr_id, $type );
					do_action( 'wpml_set_element_language_details', array( 'element_id' => $id, 'element_type' => $type, 'trid' => $trid, 'language_code' => $lang, 'source_language_code' => $default ) );
				}
			} elseif ( 'fr' === $lang ) {
				$fr_id = $id;
			}
			if ( $with_images && ! empty( $item['image'] ) ) {
				rdv_import_thumbnail( $id, $item['image'], isset( $item['image_meta'] ) ? $item['image_meta'] : array() );
			}
			$ids[ $item['key'] ][ $lang ] = $id;
			$log[]                       = sprintf( '%s (%s) → #%d %s', $item['key'], $lang, $id, $l['title'] );
		}
	}
	if ( $wpml ) {
		do_action( 'wpml_switch_language', $default );
	}

	if ( ! empty( $ids[ $data['front'] ]['fr'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids[ $data['front'] ]['fr'] );
		$log[] = 'Page d\'accueil définie.';
	}
	update_option( 'blogname', 'Rendez-vous à Nantes' );
	update_option( 'blogdescription', 'L’essentiel de la ville en 1 clic' );
	flush_rewrite_rules();
	return $log;
}

/**
 * Vrai si WPML est actif.
 *
 * @return bool
 */
function rdv_wpml_active() {
	return defined( 'ICL_SITEPRESS_VERSION' );
}

/**
 * Crée ou met à jour un contenu.
 *
 * @param array  $item Élément.
 * @param string $lang Langue.
 * @param array  $l    Données de la langue.
 * @return int|WP_Error
 */
function rdv_import_post( $item, $lang, $l ) {
	$existing = get_posts( array(
		'post_type'        => $item['type'],
		'post_status'      => 'any',
		'posts_per_page'   => 1,
		'fields'           => 'ids',
		'suppress_filters' => true,
		'meta_query'       => array( // phpcs:ignore WordPress.DB.SlowDBQuery
			array( 'key' => '_rdv_import_key', 'value' => $item['key'] . ':' . $lang ),
		),
	) );
	$post = array(
		'post_type'    => $item['type'],
		'post_status'  => 'publish',
		'post_title'   => $l['title'],
		'post_name'    => $l['slug'],
		'post_content' => $l['content'],
		'menu_order'   => (int) $item['order'],
	);
	if ( $existing ) {
		$post['ID'] = $existing[0];
		$id         = wp_update_post( wp_slash( $post ), true );
	} else {
		$id = wp_insert_post( wp_slash( $post ), true );
	}
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	update_post_meta( $id, '_rdv_import_key', $item['key'] . ':' . $lang );
	if ( ! empty( $item['template'] ) ) {
		update_post_meta( $id, '_wp_page_template', $item['template'] );
	}
	foreach ( $l['meta'] as $k => $v ) {
		// « import:fichier.jpg » (photos des listes, ex. restaurants) → identifiant du média téléversé.
		$v = preg_replace_callback( '/import:([\w.-]+)/', function ( $m ) use ( $id ) {
			$att = rdv_import_media( $m[1], $id );
			return $att ? (string) $att : '';
		}, $v );
		update_post_meta( $id, '_rdv_' . $k, wp_slash( $v ) );
	}
	// Réglages SEO du site actuel, au format Yoast (lus aussi par le thème si Yoast est absent).
	foreach ( ( isset( $l['seo'] ) ? $l['seo'] : array() ) as $k => $v ) {
		update_post_meta( $id, '_yoast_wpseo_' . $k, wp_slash( $v ) );
	}
	return $id;
}

/**
 * Associe une image à la une (téléversée une seule fois), avec texte alternatif et crédit en légende.
 *
 * @param int    $post_id Contenu.
 * @param string $file    Nom du fichier dans import/images.
 * @param array  $meta    alt / caption.
 */
function rdv_import_thumbnail( $post_id, $file, $meta = array() ) {
	$att = rdv_import_media( $file, $post_id );
	if ( $att ) {
		set_post_thumbnail( $post_id, $att );
		rdv_import_media_meta( $att, $meta );
	}
}

/**
 * Téléverse une image de import/images (une seule fois) et renvoie l'identifiant du média.
 *
 * @param string $file    Nom du fichier.
 * @param int    $post_id Contenu auquel rattacher le média.
 * @return int 0 en cas d'échec.
 */
function rdv_import_media( $file, $post_id = 0 ) {
	if ( ! file_exists( RDV_DIR . '/import/images/' . $file ) ) {
		return 0;
	}
	$found = get_posts( array(
		'post_type'      => 'attachment',
		'post_status'    => 'inherit',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_rdv_import_file', // phpcs:ignore WordPress.DB.SlowDBQuery
		'meta_value'     => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
		'suppress_filters' => true,
	) );
	if ( $found ) {
		return (int) $found[0];
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $file );
	copy( RDV_DIR . '/import/images/' . $file, $tmp );
	$att = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), $post_id );
	if ( is_wp_error( $att ) ) {
		return 0;
	}
	update_post_meta( $att, '_rdv_import_file', $file );
	return (int) $att;
}

/**
 * Texte alternatif et légende (crédit photo) d'un média importé.
 *
 * @param int   $att  Média.
 * @param array $meta alt / caption.
 */
function rdv_import_media_meta( $att, $meta ) {
	if ( ! empty( $meta['alt'] ) ) {
		update_post_meta( $att, '_wp_attachment_image_alt', $meta['alt'] );
	}
	if ( ! empty( $meta['caption'] ) ) {
		wp_update_post( array( 'ID' => $att, 'post_excerpt' => $meta['caption'] ) );
	}
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Importe le contenu initial du site Rendez-vous à Nantes.
	 *
	 * ## OPTIONS
	 *
	 * [--no-images]
	 * : Ne pas importer les images à la une.
	 */
	WP_CLI::add_command( 'rdv import', function ( $args, $assoc ) {
		foreach ( rdv_import( ! isset( $assoc['images'] ) || $assoc['images'] ) as $line ) {
			WP_CLI::log( $line );
		}
		WP_CLI::success( 'Import terminé.' );
	} );
}

add_action( 'admin_menu', function () {
	add_management_page( 'Import Rendez-vous à Nantes', 'Import Rendez-vous à Nantes', 'manage_options', 'rdv-import', function () {
		echo '<div class="wrap"><h1>Import Rendez-vous à Nantes</h1>';
		if ( isset( $_POST['rdv_import'] ) && check_admin_referer( 'rdv_import' ) ) {
			echo '<div class="notice notice-success"><p>Import terminé.</p></div><pre style="max-height:420px;overflow:auto;background:#fff;padding:12px">' . esc_html( implode( "\n", rdv_import() ) ) . '</pre>';
		}
		echo '<p>Crée (ou met à jour) les pages, rubriques et fiches du site à partir du contenu de la maquette (FR) et du site actuel (EN / ES).</p>';
		echo '<p>' . ( rdv_wpml_active() ? 'WPML est actif : les versions EN et ES seront créées et reliées aux pages françaises.' : '<strong>WPML n’est pas actif</strong> : seul le français sera importé. Activez WPML et ses langues (FR par défaut, EN, ES) avant l’import pour tout récupérer.' ) . '</p>';
		echo '<form method="post">';
		wp_nonce_field( 'rdv_import' );
		submit_button( 'Lancer l’import', 'primary', 'rdv_import' );
		echo '</form></div>';
	} );
} );
