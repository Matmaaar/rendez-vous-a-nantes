<?php
/**
 * Champs personnalisés natifs (méta-boxes), sans plugin.
 *
 * Formats « une ligne = un élément » : les colonnes sont séparées par « | ».
 * Les liens internes s'écrivent sous forme de chemin français (/incontournable/quartier-bouffay/) :
 * le thème les convertit vers la bonne URL dans la langue affichée.
 *
 * @package rdv-nantes
 */

defined( 'ABSPATH' ) || exit;

/**
 * Groupes de champs. Chaque champ : [clé, libellé, type (text|textarea|html|check|select), aide, options].
 *
 * @return array
 */
function rdv_field_groups() {
	$rubrique = array(
		array( 'num', 'Numéro de rubrique', 'text', 'Ex. « 03 » (affiché dans l\'en-tête et sur la tuile d\'accueil).' ),
		array( 'h1', 'Grand titre', 'text', 'Titre affiché en tête de page. Le titre WordPress sert de libellé court (menu, fil d\'Ariane, tuiles).' ),
		array( 'lead', 'Chapô', 'textarea', '' ),
		array( 'tile_desc', 'Texte de la tuile d\'accueil', 'text', 'Ex. « 10 bonnes raisons de faire le Voyage à Nantes ».' ),
		array( 'home', 'Afficher en tuile sur l\'accueil et dans « Autres rubriques »', 'check', '' ),
		array( 'nav', 'Afficher dans le menu principal', 'check', '' ),
	);
	$infos = array( 'infos', 'Encadré « infos pratiques »', 'textarea', 'Une ligne par info : Libellé | Valeur | 1 (facultatif : pastille verte « en direct »).' );

	return array(
		'front'         => array(
			'title'  => 'Page d\'accueil',
			'fields' => array(
				array( 'bonplan_label', 'Bandeau jaune : pastille', 'text', 'Ex. « Le bon plan ».' ),
				array( 'bonplan_text', 'Bandeau jaune : texte', 'text', '' ),
				array( 'bonplan_btn', 'Bandeau jaune : bouton', 'text', 'Libellé | lien' ),
				array( 'hero_title', 'Grand titre', 'html', 'Retour à la ligne : <br>' ),
				array( 'hero_lead', 'Sous-titre', 'text', '' ),
				array( 'tiles_title', 'Titre des tuiles', 'text', 'Ex. « Que voulez-vous faire ? »' ),
				array( 'parcours_title', 'Parcours : titre', 'text', '' ),
				array( 'parcours_sub', 'Parcours : sous-titre', 'text', '' ),
				array( 'plans', 'Parcours : étapes par durée', 'textarea', "Une durée par ligne « # 1 h », puis ses étapes : Titre | précision | lien." ),
				array( 'plan_foot', 'Parcours : texte sous la liste', 'html', '' ),
			),
		),
		'rubrique'      => array(
			'title'  => 'Rubrique',
			'fields' => $rubrique,
		),
		'rubrique-lieux' => array(
			'title'  => 'Liste de fiches',
			'fields' => array(
				array( 'post_type', 'Fiches à lister', 'select', '', 'post_types' ),
				array( 'encart_title', 'Encadré jaune : titre', 'text', 'Laisser vide pour ne pas afficher d\'encadré.' ),
				array( 'encart_text', 'Encadré jaune : texte', 'textarea', '' ),
				array( 'encart_btn', 'Encadré jaune : bouton', 'text', 'Libellé | lien' ),
			),
		),
		'liste'         => array(
			'title'  => 'Liste d\'adresses',
			'fields' => array(
				array( 'cta', 'Bouton sous le chapô', 'text', 'Libellé | lien (facultatif)' ),
				array( 'list_title', 'Titre de la liste', 'text', '' ),
				array( 'items', 'Éléments', 'textarea', 'Une ligne par élément : Titre | précision (facultatif) | description (facultatif) | lien | photo (facultatif : numéro du média, visible dans la médiathèque). Avec des photos, la liste devient illustrée.' ),
				array( 'link_label', 'Libellé des boutons', 'text', 'Ex. « Voir la fiche ».' ),
				array( 'after_title', 'Encadré final : titre', 'text', '' ),
				array( 'after_text', 'Encadré final : texte', 'textarea', '' ),
				array( 'after_btn1', 'Encadré final : bouton principal', 'text', 'Libellé | lien' ),
				array( 'after_btn2', 'Encadré final : bouton secondaire', 'text', 'Libellé | lien' ),
			),
		),
		'infos'         => array(
			'title'  => 'Page d\'infos',
			'fields' => array(
				$infos,
				array( 'maps', 'Lien du bouton « Itinéraire »', 'text', 'Facultatif.' ),
			),
		),
		'rdv_quartier'  => array(
			'title'  => 'Quartier',
			'fields' => array(
				array( 'num', 'Numéro', 'text', 'Affiché sur la carte et la vignette.' ),
				array( 'amb', 'Ambiance (titre de la vignette)', 'text', 'Ex. « Cocktails chic et cosy ».' ),
				array( 'where', 'Localisation', 'text', 'Ex. « Graslin · autour du théâtre ». La partie avant « · » sert d\'étiquette sur la carte.' ),
				array( 'map_x', 'Carte : position horizontale', 'text', 'En %, ex. « 25.5% ». Laisser vide pour ne pas placer de repère.' ),
				array( 'map_y', 'Carte : position verticale', 'text', 'En %, ex. « 40% ».' ),
				array( 'img_alt', 'Texte si pas d\'image à la une', 'text', 'Ex. « [Photo quartier Graslin] ».' ),
				array( 'badges', 'Étiquettes de la vignette', 'textarea', 'Une par ligne.' ),
				array( 'lead', 'Chapô', 'textarea', '' ),
				array( 'musts', 'Étapes incontournables', 'textarea', 'Une ligne par étape : Titre | description | mention jaune (facultatif)' ),
				$infos,
				array( 'groups', 'Bars', 'textarea', "Une ligne « ## Titre du groupe », puis un bar par ligne : Nom | adresse | mention jaune | description (colonnes facultatives)." ),
				array( 'note', 'Note grise en bas de page', 'textarea', '' ),
			),
		),
		'lieu'          => array(
			'title'  => 'Fiche',
			'fields' => array(
				array( 'subtitle', 'Sous-titre', 'text', '' ),
				$infos,
				array( 'maps', 'Lien « Itinéraire »', 'text', '' ),
				array( 'buy', 'Lien d\'achat / réservation', 'text', '' ),
				array( 'buy_label', 'Libellé du bouton d\'achat', 'text', 'Par défaut : « Acheter en ligne ».' ),
				array( 'more', 'Lien « En savoir plus »', 'text', '' ),
				array( 'link', 'Bouton vers une autre page', 'text', 'Libellé | lien' ),
			),
		),
	);
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
 * @param WP_Post $post Contenu.
 * @return string[]
 */
function rdv_groups_for( $post ) {
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
	if ( (int) get_option( 'page_on_front' ) === (int) $post->ID || rdv_is_front_translation( $post->ID ) ) {
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

add_action( 'add_meta_boxes', function ( $post_type, $post ) {
	if ( ! $post instanceof WP_Post ) {
		return;
	}
	$groups = rdv_groups_for( $post );
	$all    = rdv_field_groups();
	foreach ( $groups as $g ) {
		add_meta_box( 'rdv-' . $g, $all[ $g ]['title'], 'rdv_render_box', null, 'normal', 'high', array( 'group' => $g ) );
	}
	if ( 'page' === $post_type && ! $groups ) {
		add_meta_box( 'rdv-help', 'Rendez-vous à Nantes', function () {
			echo '<p>Choisissez un gabarit de rubrique (Attributs de la page → Modèle) puis enregistrez pour faire apparaître les champs de rubrique.</p>';
		}, null, 'side' );
	}
}, 10, 2 );

/**
 * Affiche une méta-box.
 *
 * @param WP_Post $post Contenu.
 * @param array   $box  Arguments.
 */
function rdv_render_box( $post, $box ) {
	$group = $box['args']['group'];
	$defs  = rdv_field_groups()[ $group ]['fields'];
	wp_nonce_field( 'rdv_save', 'rdv_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( $defs as $f ) {
		list( $key, $label, $type ) = $f;
		$help  = isset( $f[3] ) ? $f[3] : '';
		$name  = 'rdv[' . $key . ']';
		$id    = 'rdv-' . $group . '-' . $key;
		$value = get_post_meta( $post->ID, '_rdv_' . $key, true );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
		switch ( $type ) {
			case 'textarea':
			case 'html':
				$rows = max( 3, min( 16, substr_count( (string) $value, "\n" ) + 2 ) );
				echo '<textarea class="large-text code" rows="' . (int) $rows . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '">' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'check':
				echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="0"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1"' . checked( $value, '1', false ) . '>';
				break;
			case 'select':
				echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"><option value="">—</option>';
				foreach ( rdv_post_type_defs() as $pt => $def ) {
					echo '<option value="' . esc_attr( $pt ) . '"' . selected( $value, $pt, false ) . '>' . esc_html( $def[1] ) . '</option>';
				}
				echo '</select>';
				break;
			default:
				echo '<input type="text" class="large-text" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '">';
		}
		if ( $help ) {
			echo '<p class="description">' . esc_html( $help ) . '</p>';
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

add_action( 'save_post', function ( $post_id, $post ) {
	if ( ! isset( $_POST['rdv_nonce'], $_POST['rdv'] ) || ! wp_verify_nonce( sanitize_key( $_POST['rdv_nonce'] ), 'rdv_save' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$types = array();
	foreach ( rdv_groups_for( $post ) as $g ) {
		foreach ( rdv_field_groups()[ $g ]['fields'] as $f ) {
			$types[ $f[0] ] = $f[2];
		}
	}
	$data = wp_unslash( $_POST['rdv'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	foreach ( $types as $key => $type ) {
		if ( ! isset( $data[ $key ] ) ) {
			continue;
		}
		$v = $data[ $key ];
		if ( 'html' === $type ) {
			$v = wp_kses_post( $v );
		} elseif ( 'textarea' === $type ) {
			$v = sanitize_textarea_field( $v );
		} else {
			$v = sanitize_text_field( $v );
		}
		update_post_meta( $post_id, '_rdv_' . $key, $v );
	}
}, 10, 2 );

/**
 * Lit un champ du thème.
 *
 * @param string   $key Clé.
 * @param int|null $id  Contenu (courant par défaut).
 * @return string
 */
function rdv_meta( $key, $id = null ) {
	return (string) get_post_meta( $id ? $id : get_the_ID(), '_rdv_' . $key, true );
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
