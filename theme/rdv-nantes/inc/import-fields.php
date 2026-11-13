<?php
/**
 * Import : écriture des champs au format d'ACF.
 *
 * Le contenu de l'import (import/content.json) reste au format texte (« Libellé | lien », une ligne par élément) ;
 * il est converti ici en champs ACF : valeur « rdv_<nom> » + référence « _rdv_<nom> » vers la clé du champ,
 * et lignes numérotées pour les répéteurs (« rdv_infos_0_label »…). ACF n'a pas besoin d'être actif pour l'import.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Messages de l'import à signaler (champ inconnu…).
 *
 * @param string|null $msg Message à ajouter ; null pour lire (et vider) la liste.
 * @return string[]
 */
function rdv_import_warnings( $msg = null ) {
	static $list = array();
	if ( null !== $msg ) {
		$list[] = $msg;
		return $list;
	}
	$out  = array_values( array_unique( $list ) );
	$list = array();
	return $out;
}

/**
 * Écrit les champs d'un contenu.
 *
 * @param int      $id     Contenu.
 * @param string[] $groups Groupes de champs applicables (rdv_groups_for()).
 * @param array    $meta   Valeurs de l'import (format texte).
 */
function rdv_import_fields( $id, $groups, $meta ) {
	$all    = rdv_field_groups();
	$fields = array();
	foreach ( $groups as $g ) {
		foreach ( isset( $all[ $g ] ) ? $all[ $g ] : array() as $f ) {
			$name = substr( $f['name'], 4 ); // sans « rdv_ »
			if ( ! isset( $fields[ $name ] ) ) {
				$fields[ $name ] = $f;
			}
		}
	}
	foreach ( $meta as $k => $v ) {
		if ( ! isset( $fields[ $k ] ) ) {
			rdv_import_warnings( sprintf( 'champ « %s » sans case correspondante (%s).', $k, get_post_type( $id ) ) );
			continue;
		}
		$f = $fields[ $k ];
		switch ( $f['type'] ) {
			case 'repeater':
				if ( 'groups' === $k ) { // Bars : « ## Titre du groupe » puis un bar par ligne.
					$rows = array();
					foreach ( rdv_sections( $v, '##' ) as $sec ) {
						$rows[] = array( $sec['title'], $sec['lines'] );
					}
				} else {
					$rows = rdv_lines( $v );
				}
				rdv_import_repeater( $id, $f['name'], $f, $rows );
				break;
			case 'link':
				$p = array_pad( array_map( 'trim', explode( '|', (string) $v ) ), 2, '' );
				$v = ( $p[0] && $p[1] ) ? array(
					'title'  => $p[0],
					'url'    => $p[1],
					'target' => 0 === strpos( $p[1], 'http' ) ? '_blank' : '',
				) : '';
				rdv_import_value( $id, $f, $v );
				break;
			case 'textarea':
				rdv_import_value( $id, $f, str_replace( array( '<br>', '<br/>', '<br />' ), "\n", (string) $v ) );
				break;
			default:
				rdv_import_value( $id, $f, (string) $v );
		}
	}
}

/**
 * Enregistre une valeur de champ ACF et sa référence.
 *
 * @param int    $id   Contenu.
 * @param array  $f    Champ ACF.
 * @param mixed  $v    Valeur.
 * @param string $name Nom de la méta (par défaut, celui du champ).
 */
function rdv_import_value( $id, $f, $v, $name = '' ) {
	$name = $name ? $name : $f['name'];
	update_post_meta( $id, $name, wp_slash( $v ) );
	update_post_meta( $id, '_' . $name, $f['key'] );
}

/**
 * Enregistre un répéteur ACF (et ses répéteurs imbriqués). Les anciennes lignes sont d'abord effacées,
 * pour qu'un import rejoué avec moins de lignes ne laisse pas de restes.
 *
 * @param int     $id   Contenu.
 * @param string  $name Nom de la méta du répéteur (ex. rdv_infos, rdv_groups_0_bars).
 * @param array   $f    Champ ACF répéteur.
 * @param array[] $rows Lignes : valeurs dans l'ordre des sous-champs (une liste de lignes pour un sous-répéteur).
 */
function rdv_import_repeater( $id, $name, $f, $rows ) {
	global $wpdb;
	$like = $wpdb->esc_like( $name . '_' ) . '%';
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND ( meta_key LIKE %s OR meta_key LIKE %s )", $id, $like, $wpdb->esc_like( '_' ) . $like ) );
	wp_cache_delete( $id, 'post_meta' );
	$rows = array_values( array_filter( $rows, function ( $r ) {
		return (bool) array_filter( (array) $r, function ( $c ) {
			return is_array( $c ) ? (bool) $c : '' !== trim( (string) $c );
		} );
	} ) );
	rdv_import_value( $id, $f, count( $rows ), $name );
	foreach ( $rows as $i => $row ) {
		foreach ( $f['sub_fields'] as $j => $sub ) {
			$cell  = isset( $row[ $j ] ) ? $row[ $j ] : '';
			$mname = $name . '_' . $i . '_' . $sub['name'];
			if ( 'repeater' === $sub['type'] ) {
				rdv_import_repeater( $id, $mname, $sub, is_array( $cell ) ? $cell : array() );
			} elseif ( 'true_false' === $sub['type'] ) {
				rdv_import_value( $id, $sub, '' !== trim( (string) $cell ) ? '1' : '0', $mname );
			} else {
				rdv_import_value( $id, $sub, trim( (string) $cell ), $mname );
			}
		}
	}
}
