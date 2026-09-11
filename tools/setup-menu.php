<?php
/**
 * Monta o menu principal do Quality Blog.
 *
 * O menu e conteudo, nao codigo: ele nao viaja junto com o tema. Toda
 * instalacao nova comeca com o cabecalho vazio ate alguem montar o menu.
 * Este script faz isso de uma vez, espelhando o prototipo.
 *
 * Uso:
 *   WP_PUBLIC=/caminho/para/o/wordpress php tools/setup-menu.php
 *
 * Os itens de Software e Free Assets ficam em "#" ate alguem preencher as
 * URLs reais em Aparencia > Menus. O dropdown Categories aponta para as
 * categorias que existirem no momento em que o script roda.
 */

$public = getenv( 'WP_PUBLIC' );
if ( ! $public || ! file_exists( $public . '/wp-load.php' ) ) {
	fwrite( STDERR, "Defina WP_PUBLIC apontando para a pasta do WordPress.\n" );
	exit( 1 );
}

define( 'WP_USE_THEMES', false );
if ( getenv( 'DB_SOCKET' ) ) {
	define( 'DB_HOST', 'localhost:' . getenv( 'DB_SOCKET' ) );
}
require $public . '/wp-load.php';

$name = 'Quality Blog - principal';
$old  = wp_get_nav_menu_object( $name );
if ( $old ) {
	wp_delete_nav_menu( $old->term_id );
	echo "menu anterior removido\n";
}

$menu_id = wp_create_nav_menu( $name );
if ( is_wp_error( $menu_id ) ) {
	fwrite( STDERR, "Falha ao criar o menu: " . $menu_id->get_error_message() . "\n" );
	exit( 1 );
}

/**
 * Item de menu do tipo link.
 */
function qb_menu_item( $menu_id, $title, $url = '#', $parent = 0, $classes = '' ) {
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

$software = qb_menu_item( $menu_id, 'Software for Quality' );
foreach ( array(
	'Document Management',
	'Nonconformities',
	'Risk Management',
	'Audit Management',
	'Meeting Minutes',
	'Action Plans',
	'Indicator Management',
	'Process Flows',
	'Training Management',
	'Measurement Instruments',
	'Supplier Management',
	'Corporate Education',
) as $label ) {
	qb_menu_item( $menu_id, $label, '#', $software );
}
echo "Software for Quality -> 12 itens (URLs a preencher)\n";

$categories = qb_menu_item( $menu_id, 'Categories' );
$count      = 0;
foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => true ) ) as $term ) {
	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $term->name,
			'menu-item-object'    => 'category',
			'menu-item-object-id' => $term->term_id,
			'menu-item-type'      => 'taxonomy',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $categories,
		)
	);
	$count++;
}
echo "Categories           -> $count categorias reais\n";

$assets = qb_menu_item( $menu_id, 'Free Assets' );
foreach ( array( 'Downloadable Materials', 'Webinars & Events', 'ISO 9001 Guide' ) as $label ) {
	qb_menu_item( $menu_id, $label, '#', $assets );
}
echo "Free Assets          -> 3 itens (URLs a preencher)\n";

// Item solto. A classe qa-link aciona o destaque azul no tema.
qb_menu_item( $menu_id, 'Quality Assistant', '#', 0, 'qa-link' );
echo "Quality Assistant    -> item solto, classe qa-link\n";

$locations            = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary'] = $menu_id;
$locations['mobile']  = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );
echo "\natribuido aos locais: primary, mobile\n";
