<?php
/**
 * Fonctions d'affichage : équivalents PHP des fonctions de rendu de la maquette.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;

/** Pictogramme repère (maquette : ICON_PIN). */
const RDV_ICON_PIN = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" aria-hidden="true"><path d="M12 21s-7-7.4-7-12a7 7 0 0 1 14 0c0 4.6-7 12-7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>';

/**
 * Convertit un lien saisi dans un champ en URL.
 * Les chemins internes (« /incontournable/quartier-bouffay/ ») sont résolus vers le contenu
 * correspondant, dans la langue affichée.
 *
 * @param string $link Lien saisi.
 * @return string
 */
function rdv_link( $link ) {
	$link = trim( (string) $link );
	$home = untrailingslashit( home_url() );
	if ( '' !== $link && 0 === strpos( $link, $home . '/' ) ) {
		// Adresse complète du site (lien choisi dans la liste d'ACF) : traitée comme un chemin interne.
		$link = substr( $link, strlen( $home ) );
	}
	if ( '' === $link || '/' !== $link[0] ) {
		return $link;
	}
	$path = trim( wp_parse_url( $link, PHP_URL_PATH ), '/' );
	if ( '' === $path ) {
		return apply_filters( 'wpml_home_url', home_url( '/' ) );
	}
	$parts = explode( '/', $path );
	$post  = null;
	if ( 2 === count( $parts ) ) {
		foreach ( rdv_post_type_defs() as $type => $def ) {
			if ( $def[0] === $parts[0] ) {
				$post = rdv_get_by_slug( $parts[1], $type );
				break;
			}
		}
	}
	if ( ! $post ) {
		$post = rdv_get_by_slug( $path, 'page' );
	}
	if ( ! $post ) {
		return home_url( '/' . $path . '/' );
	}
	$id = apply_filters( 'wpml_object_id', $post->ID, $post->post_type, true );
	return get_permalink( $id );
}

/**
 * Trouve un contenu par son slug, toutes langues confondues (de préférence en français).
 *
 * @param string $slug Slug ou chemin.
 * @param string $type Type de contenu.
 * @return WP_Post|null
 */
function rdv_get_by_slug( $slug, $type ) {
	static $cache = array();
	$k = $type . ':' . $slug;
	if ( ! array_key_exists( $k, $cache ) ) {
		$cache[ $k ] = get_page_by_path( $slug, OBJECT, $type );
	}
	return $cache[ $k ];
}

/**
 * Bouton vers un lien externe (maquette : ext()). Les liens internes s'ouvrent dans le même onglet.
 *
 * @param string $url   Lien (externe ou chemin interne).
 * @param string $label Libellé.
 * @param string $cls   Classes.
 * @return string
 */
function rdv_btn( $url, $label, $cls = 'btn sm' ) {
	$href     = rdv_link( $url );
	$external = 0 !== strpos( $href, home_url() ) && 0 === strpos( $href, 'http' );
	return sprintf(
		'<a class="%s" href="%s"%s>%s</a>',
		esc_attr( $cls ),
		esc_url( $href ),
		$external ? ' target="_blank" rel="noopener"' : '',
		esc_html( $label )
	);
}

/**
 * Bouton à partir d'un champ « Lien » d'ACF (texte + adresse), ou d'un texte « Libellé | lien ».
 *
 * @param array|string $field  Valeur du champ.
 * @param string       $cls    Classes.
 * @param string       $suffix Ajouté au libellé (ex. « → »).
 * @return string
 */
function rdv_btn_field( $field, $cls = 'btn plein', $suffix = '' ) {
	if ( is_array( $field ) ) {
		$p = array( isset( $field['title'] ) ? trim( $field['title'] ) : '', isset( $field['url'] ) ? trim( $field['url'] ) : '' );
	} else {
		$p = array_pad( array_map( 'trim', explode( '|', (string) $field ) ), 2, '' );
	}
	return ( $p[0] && $p[1] ) ? rdv_btn( $p[1], $p[0] . $suffix, $cls ) : '';
}

/**
 * Lien Google Maps de recherche (maquette : gmaps()).
 *
 * @param string $q Requête.
 * @return string
 */
function rdv_gmaps( $q ) {
	return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $q );
}

/**
 * Pages de rubrique (gabarits rubrique) dans l'ordre, langue courante.
 *
 * @param string $flag 'home' ou 'nav' pour filtrer.
 * @return WP_Post[]
 */
function rdv_rubriques( $flag = '' ) {
	$args = array(
		'post_type'        => 'page',
		'posts_per_page'   => -1,
		'orderby'          => 'menu_order',
		'order'            => 'ASC',
		'suppress_filters' => false,
		'meta_query'       => array(
			array( 'key' => '_wp_page_template', 'value' => array_keys( rdv_rubrique_templates() ), 'compare' => 'IN' ),
		),
	);
	if ( $flag ) {
		$args['meta_query'][] = array( 'key' => 'rdv_' . $flag, 'value' => '1' );
	}
	return get_posts( $args );
}

/**
 * Page de rubrique qui liste un type de fiches.
 *
 * @param string $type Type de contenu.
 * @return WP_Post|null
 */
function rdv_rubrique_for_type( $type ) {
	if ( 'rdv_quartier' === $type ) { // Les quartiers ont leur propre gabarit de rubrique.
		$pages = get_posts( array(
			'post_type'        => 'page',
			'posts_per_page'   => 1,
			'suppress_filters' => false,
			'meta_key'         => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'       => 'page-templates/rubrique-quartiers.php', // phpcs:ignore WordPress.DB.SlowDBQuery
		) );
		return $pages ? $pages[0] : null;
	}
	$pages = get_posts( array(
		'post_type'        => 'page',
		'posts_per_page'   => 1,
		'suppress_filters' => false,
		'meta_key'         => 'rdv_post_type', // phpcs:ignore WordPress.DB.SlowDBQuery
		'meta_value'       => $type, // phpcs:ignore WordPress.DB.SlowDBQuery
	) );
	return $pages ? $pages[0] : null;
}

/**
 * Rubrique active (pour le menu).
 *
 * @return int ID de page ou 0.
 */
function rdv_current_rubrique_id() {
	if ( is_page() && isset( rdv_rubrique_templates()[ get_page_template_slug() ] ) ) {
		return get_the_ID();
	}
	if ( is_singular( rdv_post_types() ) ) {
		$r = rdv_rubrique_for_type( get_post_type() );
		return $r ? $r->ID : 0;
	}
	return 0;
}

/**
 * Fil d'Ariane (maquette : crumb()).
 *
 * @param array $items Liste de [libellé, url] ; le dernier est la page courante.
 */
function rdv_crumb( $items ) {
	echo '<div class="wrap crumb"><a href="' . esc_url( apply_filters( 'wpml_home_url', home_url( '/' ) ) ) . '">' . esc_html__( 'Accueil', 'rdv-nantes' ) . '</a>';
	$last = count( $items ) - 1;
	foreach ( $items as $i => $it ) {
		echo '<span aria-hidden="true">/</span>';
		if ( $i < $last && ! empty( $it[1] ) ) {
			echo '<a href="' . esc_url( $it[1] ) . '">' . esc_html( $it[0] ) . '</a>';
		} else {
			echo '<strong>' . esc_html( $it[0] ) . '</strong>';
		}
	}
	echo '</div>';
}

/**
 * En-tête de page de rubrique : titre et chapô (sans surtitre, qui répétait le titre).
 *
 * @param string $title Titre.
 * @param string $lead  Chapô.
 */
function rdv_head( $title, $lead = '' ) {
	echo '<section class="pagehead"><div class="wrap"><h1 class="big">' . rdv_nbsp( esc_html( $title ) ) . '</h1>';
	if ( $lead ) {
		echo '<p class="lead" style="margin-top:22px">' . wp_kses_post( $lead ) . '</p>';
	}
	echo '</div></section>';
}

/**
 * Barre « Autres rubriques » (maquette : othersBar()).
 *
 * @param int $exclude Page de rubrique courante.
 */
function rdv_others( $exclude ) {
	echo '<section class="others"><div class="wrap"><span class="label" style="margin-right:8px">' . esc_html__( 'Autres rubriques', 'rdv-nantes' ) . '</span>';
	foreach ( rdv_rubriques( 'home' ) as $r ) {
		if ( (int) $r->ID !== (int) $exclude ) {
			echo '<a class="chip" href="' . esc_url( get_permalink( $r ) ) . '">' . esc_html( get_the_title( $r ) ) . '</a>';
		}
	}
	echo '</div></section>';
}

/**
 * Encadré d'infos pratiques (maquette : asideInfos()).
 *
 * @param array[] $infos Lignes [libellé, valeur, en direct] (voir rdv_infos()).
 * @param string  $btns  HTML des boutons.
 */
function rdv_aside( $infos, $btns = '' ) {
	echo '<aside class="infos">';
	foreach ( $infos as $row ) {
		$live = ! empty( $row[2] );
		echo '<div><p class="label" style="font-size:12px">' . esc_html( $row[0] ) . '</p><p class="v">' . ( $live ? '<span class="dot"></span>' : '' ) . esc_html( isset( $row[1] ) ? $row[1] : '' ) . '</p></div>';
	}
	if ( $btns ) {
		echo '<div class="btns" style="flex-direction:column">' . $btns . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput -- construit avec rdv_btn().
	}
	echo '</aside>';
}

/**
 * Sélecteur de langue (WPML), au format de la maquette : FR · EN · ES.
 */
function rdv_language_switcher() {
	$langs = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0, 'orderby' => 'custom' ) );
	if ( is_array( $langs ) ) {
		// Toujours FR · EN · ES, quel que soit l'ordre réglé dans WPML (les autres langues éventuelles à la suite).
		$rank = array_flip( array( 'fr', 'en', 'es' ) );
		uasort( $langs, function ( $a, $b ) use ( $rank ) {
			$ra = isset( $rank[ $a['code'] ] ) ? $rank[ $a['code'] ] : 99;
			$rb = isset( $rank[ $b['code'] ] ) ? $rank[ $b['code'] ] : 99;
			return $ra - $rb;
		} );
	}
	// Visible sur tous les écrans (la maquette le masquait sur mobile).
	echo '<div class="langs" role="navigation" aria-label="' . esc_attr__( 'Langue', 'rdv-nantes' ) . '">';
	if ( empty( $langs ) ) {
		// Sans WPML (démo, serveur de test) : seul le français existe ; EN et ES sont affichés grisés.
		echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="on" aria-current="true">FR</a>';
		echo '<span class="off" title="' . esc_attr__( 'Disponible une fois WPML activé', 'rdv-nantes' ) . '">EN</span>';
		echo '<span class="off" title="' . esc_attr__( 'Disponible une fois WPML activé', 'rdv-nantes' ) . '">ES</span>';
	} else {
		foreach ( $langs as $l ) {
			$on = ! empty( $l['active'] );
			printf(
				'<a href="%s" hreflang="%s" lang="%s"%s>%s</a>',
				esc_url( $l['url'] ),
				esc_attr( $l['code'] ),
				esc_attr( $l['code'] ),
				$on ? ' class="on" aria-current="true"' : '',
				esc_html( strtoupper( $l['code'] ) )
			);
		}
	}
	echo '</div>';
}

/**
 * Pager bas de fiche : précédent / suivant dans l'ordre des fiches.
 *
 * @param WP_Post[] $items Fiches.
 * @param int       $id    Fiche courante.
 * @return array [index, total, suivante]
 */
function rdv_position( $items, $id ) {
	$ids = wp_list_pluck( $items, 'ID' );
	$i   = array_search( $id, $ids ); // phpcs:ignore WordPress.PHP.StrictInArray
	$i   = false === $i ? 0 : (int) $i;
	$n   = count( $items );
	return array( $i, $n, $n ? $items[ ( $i + 1 ) % $n ] : null );
}

/**
 * Libellé court d'une rubrique et son URL, pour une fiche.
 *
 * @param string $type Type de fiche.
 * @return array [libellé, url, id]
 */
function rdv_rubrique_info( $type ) {
	$r = rdv_rubrique_for_type( $type );
	if ( $r ) {
		return array( get_the_title( $r ), get_permalink( $r ), $r->ID );
	}
	$defs = rdv_post_type_defs();
	return array( $defs[ $type ][1], home_url( '/' . $defs[ $type ][0] . '/' ), 0 );
}

/**
 * Résout les liens internes (href="/chemin/") d'un texte riche vers la langue affichée.
 *
 * @param string $html HTML.
 * @return string
 */
function rdv_resolve_links( $html ) {
	return preg_replace_callback( '#href="(/[^"/][^"]*|/)"#', function ( $m ) {
		return 'href="' . esc_url( rdv_link( $m[1] ) ) . '"';
	}, (string) $html );
}
add_filter( 'the_content', 'rdv_resolve_links', 20 );

/**
 * Mosaïque photo : cases collées, photo pleine case, titre et accroche sur un halo diffus.
 *
 * @param array $tiles Cases : [ 'url', 'title', 'desc', 'post' (contenu dont on prend l'image à la une),
 *                     'num' (pastille facultative), 'tags' (étiquettes facultatives) ].
 * @param int   $cols  Colonnes sur grand écran ; 0 = automatique (4 à partir de 8 cases, sinon 3).
 */
function rdv_mosaic( $tiles, $cols = 0 ) {
	if ( ! $tiles ) {
		return;
	}
	$cols = $cols ? (int) $cols : ( count( $tiles ) >= 8 ? 4 : 3 );
	echo '<div class="mosaic" style="--cols:' . (int) $cols . '">';
	foreach ( array_values( $tiles ) as $i => $t ) {
		echo '<a class="mtile" href="' . esc_url( $t['url'] ) . '">';
		if ( ! empty( $t['post'] ) && has_post_thumbnail( $t['post'] ) ) {
			// 1re photo : chargée en priorité (c'est la plus grande image visible à l'arrivée, sur mobile).
			// Les suivantes en différé : le navigateur charge tout de suite celles déjà à l'écran.
			echo get_the_post_thumbnail( $t['post'], 'rdv-card', array(
				'alt'           => '',
				'loading'       => 0 === $i ? 'eager' : 'lazy',
				'fetchpriority' => 0 === $i ? 'high' : 'auto',
				'sizes'         => '(max-width:640px) 92vw, (max-width:1000px) 46vw, ' . ( 4 === $cols ? '300px' : '400px' ),
			) );
		}
		if ( ! empty( $t['num'] ) ) {
			echo '<span class="num">' . esc_html( $t['num'] ) . '</span>';
		}
		echo '<span class="body"><span class="t">' . esc_html( $t['title'] ) . '</span>';
		if ( ! empty( $t['desc'] ) ) {
			echo '<span class="d">' . esc_html( $t['desc'] ) . '</span>';
		}
		if ( ! empty( $t['tags'] ) ) {
			echo '<span class="tags">';
			foreach ( $t['tags'] as $tag ) {
				echo '<span>' . esc_html( $tag ) . '</span>';
			}
			echo '</span>';
		}
		echo '</span></a>';
	}
	echo '</div>';
}

/**
 * Photo d'une fiche : la même image à la une que sa case dans la mosaïque, avec le crédit en légende.
 *
 * @param string $style Style en ligne facultatif (marges).
 */
function rdv_fiche_photo( $style = '' ) {
	if ( ! has_post_thumbnail() ) {
		return;
	}
	$id      = get_post_thumbnail_id();
	$caption = wp_get_attachment_caption( $id );
	echo '<figure class="fiche-photo"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '>';
	echo wp_get_attachment_image( $id, 'large', false, array( 'sizes' => '(max-width:1180px) 92vw, 740px', 'fetchpriority' => 'high' ) );
	if ( $caption ) {
		echo '<figcaption>' . esc_html( $caption ) . '</figcaption>';
	}
	echo '</figure>';
}

/**
 * Typographie française : espace insécable avant ? ! : ; (évite un signe seul en début de ligne).
 *
 * @param string $html Texte déjà échappé.
 * @return string
 */
function rdv_nbsp( $html ) {
	return preg_replace( '/ ([?!:;])/u', '&nbsp;$1', $html );
}
