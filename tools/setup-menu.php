<?php
/**
 * Monta os menus do tema numa instalacao nova.
 *
 * Menu e conteudo, nao codigo: ele mora no banco e nao viaja dentro do zip.
 * Toda instalacao nova comeca com o cabecalho e o rodape vazios ate alguem
 * montar os menus. Este script faz isso de uma vez.
 *
 * Uso:
 *   WP_PUBLIC=/caminho/do/wordpress php tools/setup-menu.php          (ingles)
 *   WP_PUBLIC=/caminho/do/wordpress LANG=es php tools/setup-menu.php  (espanhol)
 *
 * As URLs ficam em "#" ate alguem preencher os enderecos reais em
 * Aparencia > Menus. O dropdown Categories aponta para as categorias que
 * existirem no momento em que o script roda - por isso rode-o depois de
 * criar ou importar o conteudo.
 */

$public = getenv( 'WP_PUBLIC' );
if ( ! $public || ! file_exists( $public . '/wp-load.php' ) ) {
	fwrite( STDERR, "Defina WP_PUBLIC apontando para a pasta do WordPress.\n" );
	exit( 1 );
}

define( 'WP_USE_THEMES', false );
require $public . '/wp-load.php';

$lang = 'es' === strtolower( (string) getenv( 'LANG' ) ) ? 'es' : 'en';

/**
 * Rotulos dos itens. Sao os mesmos do prototipo.
 */
function qb_labels( $lang ) {
	$en = array(
		'menu_name'   => 'Quality Blog - principal',
		'software'    => 'Software for Quality',
		'software_items' => array(
			'Document Management', 'Nonconformities', 'Risk Management', 'Audit Management',
			'Meeting Minutes', 'Action Plans', 'Indicator Management', 'Process Flows',
			'Training Management', 'Measurement Instruments', 'Supplier Management', 'Corporate Education',
		),
		'categories'  => 'Categories',
		'assets'      => 'Free Assets',
		'assets_items' => array( 'Downloadable Materials', 'Webinars & Events', 'ISO 9001 Guide' ),
		'assistant'   => 'Quality Assistant',
		'foot_resources'      => 'Rodape Resources',
		'foot_resources_items' => array( 'Free Assets', 'Quality Assistant', 'Quality Gurus', 'Business Strategy' ),
		'foot_software'       => 'Rodape Software',
		'foot_software_items' => array( 'Qualiex for Quality', 'Book a demo', 'Customer stories', 'Pricing' ),
		'foot_legal'          => 'Rodape legal',
		'foot_legal_items'    => array( 'Privacy Policy', 'Cookie Settings' ),
	);

	$es = array(
		'menu_name'   => 'Blog de la Calidad - principal',
		'software'    => 'Software para Calidad',
		'software_items' => array(
			'Gestión de Documentos', 'No Conformidades', 'Gestión de Riesgos', 'Gestión de Auditorías',
			'Actas de Reunión', 'Planes de Acción', 'Gestión de Indicadores', 'Flujos de Proceso',
			'Gestión de Capacitación', 'Instrumentos de Medición', 'Gestión de Proveedores', 'Educación Corporativa',
		),
		'categories'  => 'Categorías',
		'assets'      => 'Recursos Gratuitos',
		'assets_items' => array( 'Materiales Descargables', 'Webinars y Eventos', 'Guía ISO 9001' ),
		'assistant'   => 'Quality Assistant',
		'foot_resources'      => 'Pie Recursos',
		'foot_resources_items' => array( 'Recursos Gratuitos', 'Quality Assistant', 'Gurús de la Calidad', 'Estrategia de Negocio' ),
		'foot_software'       => 'Pie Software',
		'foot_software_items' => array( 'Qualiex para Calidad', 'Solicitar una demo', 'Casos de éxito', 'Precios' ),
		'foot_legal'          => 'Pie legal',
		'foot_legal_items'    => array( 'Política de privacidad', 'Configuración de cookies' ),
	);

	return 'es' === $lang ? $es : $en;
}

/**
 * Cria (ou recria) um menu e devolve o id.
 */
function qb_menu( $name ) {
	$old = wp_get_nav_menu_object( $name );
	if ( $old ) {
		wp_delete_nav_menu( $old->term_id );
	}

	return wp_create_nav_menu( $name );
}

/**
 * Item de menu do tipo link.
 */
function qb_item( $menu_id, $title, $url = '#', $parent = 0, $classes = '' ) {
	return wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $title,
			'menu-item-url'       => $url,
			'menu-item-status'    => 'publish',
			'menu-item-type'      => 'custom',
			'menu-item-parent-id' => $parent,
			'menu-item-classes'   => $classes,
		)
	);
}

$L         = qb_labels( $lang );
$locations = get_theme_mod( 'nav_menu_locations', array() );

echo "idioma: $lang\n\n";

/* ---- menu principal ---- */

$main = qb_menu( $L['menu_name'] );

$software = qb_item( $main, $L['software'] );
foreach ( $L['software_items'] as $label ) {
	qb_item( $main, $label, '#', $software );
}
echo "principal / {$L['software']}: " . count( $L['software_items'] ) . " itens\n";

$cats  = qb_item( $main, $L['categories'] );
$count = 0;
foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => true ) ) as $term ) {
	wp_update_nav_menu_item(
		$main,
		0,
		array(
			'menu-item-title'     => $term->name,
			'menu-item-object'    => 'category',
			'menu-item-object-id' => $term->term_id,
			'menu-item-type'      => 'taxonomy',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $cats,
		)
	);
	$count++;
}
echo "principal / {$L['categories']}: $count categorias reais\n";

$assets = qb_item( $main, $L['assets'] );
foreach ( $L['assets_items'] as $label ) {
	qb_item( $main, $label, '#', $assets );
}
echo "principal / {$L['assets']}: " . count( $L['assets_items'] ) . " itens\n";

qb_item( $main, $L['assistant'], '#', 0, 'qa-link' );
echo "principal / {$L['assistant']}: item solto (classe qa-link)\n";

$locations['primary'] = $main;
$locations['mobile']  = $main;

/* ---- menus do rodape ---- */

foreach ( array(
	'footer-resources' => array( $L['foot_resources'], $L['foot_resources_items'] ),
	'footer-software'  => array( $L['foot_software'], $L['foot_software_items'] ),
	'footer-legal'     => array( $L['foot_legal'], $L['foot_legal_items'] ),
) as $location => $conf ) {
	list( $name, $items ) = $conf;

	$menu_id = qb_menu( $name );
	foreach ( $items as $label ) {
		qb_item( $menu_id, $label );
	}
	$locations[ $location ] = $menu_id;

	echo "rodape / $name: " . count( $items ) . " itens\n";
}

set_theme_mod( 'nav_menu_locations', $locations );

echo "\natribuidos: primary, mobile, footer-resources, footer-software, footer-legal\n";
echo "Falta preencher as URLs reais em Aparencia > Menus.\n";
