<?php
/**
 * Champs personnalisés : ACF PRO.
 *
 * Les cases sont définies dans acf-json/ (« Local JSON » d'ACF, généré par tools/build_acf.py) : elles apparaissent
 * dans l'admin dès qu'ACF PRO est actif, et se modifient dans ACF → Groupes de champs.
 * L'affichage du site lit directement les valeurs enregistrées par ACF : il fonctionne même si ACF est désactivé
 * (seule la saisie dans l'admin a besoin d'ACF).
 *
 * Les liens internes s'écrivent sous forme de chemin français (/incontournable/quartier-bouffay/) ou d'adresse
 * complète du site : le thème les convertit vers la bonne URL dans la langue affichée.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Groupes de champs ACF du thème, lus dans acf-json/.
 *
 * @return array groupe => liste de champs ACF (avec sous-champs)
 */
function rdv_field_groups() {
	static $groups = null;
	if ( null === $groups ) {
		$groups = array();
		foreach ( (array) glob( RDV_DIR . '/acf-json/group_rdv_*.json' ) as $file ) {
			$g = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			if ( $g ) {
				$groups[ str_replace( '_', '-', substr( $g['key'], strlen( 'group_rdv_' ) ) ) ] = $g['fields'];
			}
		}
		// Les identifiants de groupe gardent leur tiret historique (rubrique-lieux) ; rdv_quartier garde le sien.
		if ( isset( $groups['rdv-quartier'] ) ) {
			$groups['rdv_quartier'] = $groups['rdv-quartier'];
			unset( $groups['rdv-quartier'] );
		}
	}
	return $groups;
}

/**
 * Gabarits de page « rubrique ».
 *
 * @return array fichier => libellé
 */
function rdv_rubrique_templates() {
	return array(
		'page-templates/rubrique-quartiers.php' => 'rubrique-quartiers',
		'page-templates/rubrique-lieux.php'     => 'rubrique-lieux',
		'page-templates/liste.php'              => 'liste',
		'page-templates/infos.php'              => 'infos',
	);
}

/**
 * Groupes applicables à un contenu.
 *
 * @param WP_Post   $post     Contenu.
 * @param bool|null $is_front Forcer « page d'accueil » (import) ; null = d'après les réglages.
 * @return string[]
 */
function rdv_groups_for( $post, $is_front = null ) {
	if ( 'rdv_quartier' === $post->post_type ) {
		return array( 'rdv_quartier' );
	}
	if ( in_array( $post->post_type, rdv_post_types(), true ) ) {
		return array( 'lieu' );
	}
	if ( 'page' !== $post->post_type ) {
		return array();
	}
	$groups = array();
	if ( null === $is_front ? ( (int) get_option( 'page_on_front' ) === (int) $post->ID || rdv_is_front_translation( $post->ID ) ) : $is_front ) {
		$groups[] = 'front';
	}
	$tpl = get_page_template_slug( $post );
	$map = rdv_rubrique_templates();
	if ( isset( $map[ $tpl ] ) ) {
		$groups[] = 'rubrique';
		if ( 'rubrique-quartiers' !== $map[ $tpl ] ) {
			$groups[] = $map[ $tpl ];
		}
	}
	return $groups;
}

/**
 * Vrai si la page est une traduction WPML de la page d'accueil.
 *
 * @param int $id Page.
 * @return bool
 */
function rdv_is_front_translation( $id ) {
	$front = (int) get_option( 'page_on_front' );
	if ( ! $front ) {
		return false;
	}
	$trid = apply_filters( 'wpml_element_trid', null, $front, 'post_page' );
	return $trid && (int) apply_filters( 'wpml_element_trid', null, $id, 'post_page' ) === (int) $trid;
}

// Aide dans l'admin : gabarit à choisir, ou ACF absent.
add_action( 'add_meta_boxes', function ( $post_type, $post ) {
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	$groups = rdv_groups_for( $post );
	if ( 'page' === $post_type && ! $groups ) {
		add_meta_box( 'rdv-help', 'Rendez-vous à Nantes', function () {
			echo '<p>Choisissez un gabarit de rubrique (Attributs de la page → Modèle) puis enregistrez pour faire apparaître les champs de rubrique.</p>';
		}, null, 'side' );
	}
	if ( $groups && ! function_exists( 'acf_get_field_groups' ) ) {
		add_meta_box( 'rdv-acf', 'Rendez-vous à Nantes', function () {
			echo '<p><strong>Les champs de cette page ne sont pas modifiables :</strong> activez l’extension Advanced Custom Fields PRO. Le site, lui, s’affiche normalement.</p>';
		}, null, 'normal', 'high' );
	}
}, 10, 2 );

/**
 * Groupes ACF de l'ancien thème : ceux qui existaient avant l'activation de ce thème.
 * Notés une fois (à l'activation, ou au premier passage dans l'admin avec ACF actif), puis masqués sur les écrans
 * des pages et fiches gérées par ce thème, pour ne pas mélanger anciennes et nouvelles cases.
 * Ils ne sont ni modifiés ni supprimés : l'ancien thème les retrouve tels quels en cas de retour arrière.
 * Les groupes créés ensuite (par ex. par la personne qui fait évoluer le site) restent visibles.
 */
function rdv_note_legacy_acf_groups() {
	if ( false !== get_option( 'rdv_legacy_acf_groups' ) || ! function_exists( 'acf_get_field_groups' ) ) {
		return;
	}
	$keys = array();
	foreach ( acf_get_field_groups() as $g ) {
		if ( 0 !== strpos( $g['key'], 'group_rdv_' ) ) {
			$keys[] = $g['key'];
		}
	}
	update_option( 'rdv_legacy_acf_groups', $keys, false );
}
add_action( 'after_switch_theme', 'rdv_note_legacy_acf_groups' );
add_action( 'admin_init', 'rdv_note_legacy_acf_groups' );

add_filter( 'acf/load_field_groups', function ( $groups ) {
	$legacy = get_option( 'rdv_legacy_acf_groups' );
	if ( ! $legacy || ! is_admin() || ! function_exists( 'get_current_screen' ) ) {
		return $groups;
	}
	$screen = get_current_screen();
	$post   = get_post();
	if ( ! $screen || 'post' !== $screen->base || ! $post || ! rdv_groups_for( $post ) ) {
		return $groups;
	}
	return array_values( array_filter( $groups, function ( $g ) use ( $legacy ) {
		return ! in_array( $g['key'], (array) $legacy, true );
	} ) );
}, 30 ); // après le chargement des groupes du thème (acf-json, priorité 20)

/**
 * Valeur brute d'un champ du thème, telle qu'ACF l'enregistre (méta « rdv_<clé> »).
 *
 * @param string   $key Clé (sans le préfixe rdv_).
 * @param int|null $id  Contenu (courant par défaut).
 * @return mixed
 */
function rdv_field( $key, $id = null ) {
	return get_post_meta( $id ? $id : get_the_ID(), 'rdv_' . $key, true );
}

/**
 * Champ texte du thème.
 *
 * @param string   $key Clé.
 * @param int|null $id  Contenu (courant par défaut).
 * @return string
 */
function rdv_meta( $key, $id = null ) {
	$v = rdv_field( $key, $id );
	return is_scalar( $v ) ? (string) $v : '';
}

/**
 * Lignes d'un champ « répéteur » ACF, lues directement dans les métadonnées (fonctionne sans ACF).
 * Chaque ligne est une liste de valeurs dans l'ordre de $subs.
 *
 * @param string   $key  Clé du répéteur (ex. « infos », ou « groups_0_bars » pour un répéteur imbriqué).
 * @param string[] $subs Sous-champs.
 * @param int|null $id   Contenu.
 * @return array[]
 */
function rdv_rows( $key, $subs, $id = null ) {
	$id   = $id ? $id : get_the_ID();
	$n    = (int) get_post_meta( $id, 'rdv_' . $key, true );
	$rows = array();
	for ( $i = 0; $i < $n; $i++ ) {
		$row = array();
		foreach ( $subs as $sub ) {
			$v     = get_post_meta( $id, 'rdv_' . $key . '_' . $i . '_' . $sub, true );
			$row[] = is_scalar( $v ) ? trim( (string) $v ) : '';
		}
		if ( array_filter( $row, 'strlen' ) ) {
			$rows[] = $row;
		}
	}
	return $rows;
}

/**
 * Infos pratiques : [libellé, valeur, en direct].
 *
 * @param int|null $id Contenu.
 * @return array[]
 */
function rdv_infos( $id = null ) {
	return rdv_rows( 'infos', array( 'label', 'value', 'live' ), $id );
}

/**
 * Groupes de bars d'un quartier : [ ['title' => …, 'lines' => [ [nom, adresse, mention, description] ] ] ].
 *
 * @param int|null $id Contenu.
 * @return array[]
 */
function rdv_bar_groups( $id = null ) {
	$id  = $id ? $id : get_the_ID();
	$out = array();
	$n   = (int) get_post_meta( $id, 'rdv_groups', true );
	for ( $i = 0; $i < $n; $i++ ) {
		$out[] = array(
			'title' => (string) get_post_meta( $id, 'rdv_groups_' . $i . '_title', true ),
			'lines' => rdv_rows( 'groups_' . $i . '_bars', array( 'name', 'addr', 'tag', 'desc' ), $id ),
		);
	}
	return $out;
}

/**
 * Découpe un champ « une ligne = un élément, colonnes séparées par | ».
 *
 * @param string $text Texte.
 * @return array[]
 */
function rdv_lines( $text ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		if ( '' === trim( $line ) ) {
			continue;
		}
		$out[] = array_map( 'trim', explode( '|', $line ) );
	}
	return $out;
}

/**
 * Découpe un champ en sections « # Titre » (ou « ## Titre ») suivies de lignes.
 *
 * @param string $text   Texte.
 * @param string $marker Marqueur de section.
 * @return array[] [ ['title' => ..., 'lines' => [...]] ]
 */
function rdv_sections( $text, $marker ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$t = trim( $line );
		if ( '' === $t ) {
			continue;
		}
		if ( 0 === strpos( $t, $marker . ' ' ) ) {
			$out[] = array( 'title' => trim( substr( $t, strlen( $marker ) + 1 ) ), 'lines' => array() );
			continue;
		}
		if ( ! $out ) {
			$out[] = array( 'title' => '', 'lines' => array() );
		}
		$out[ count( $out ) - 1 ]['lines'][] = array_map( 'trim', explode( '|', $t ) );
	}
	return $out;
}
