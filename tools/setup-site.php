<?php
/**
 * Configura uma instalacao nova para o tema Quality Blog.
 *
 * Ativa o tema, define os links permanentes, aponta a home para os posts
 * recentes e liga a moderacao manual de comentarios. Idempotente: rodar duas
 * vezes nao causa problema.
 *
 * Uso:
 *   WP_PUBLIC=/caminho/para/o/wordpress php tools/setup-site.php
 */

$public = getenv( 'WP_PUBLIC' );
if ( ! $public || ! file_exists( $public . '/wp-load.php' ) ) {
	fwrite( STDERR, "Defina WP_PUBLIC apontando para a pasta do WordPress.\n" );
	exit( 1 );
}

define( 'WP_USE_THEMES', false );
require $public . '/wp-load.php';

switch_theme( 'quality-blog' );
echo "tema        -> " . get_stylesheet() . "\n";

global $wp_rewrite;
$wp_rewrite->set_permalink_structure( '/%postname%/' );
$wp_rewrite->flush_rules( true );
echo "permalink   -> " . get_option( 'permalink_structure' ) . "\n";

update_option( 'show_on_front', 'posts' );
echo "home        -> " . get_option( 'show_on_front' ) . "\n";

update_option( 'comment_moderation', 1 );
update_option( 'comment_previously_approved', 0 );
echo "comentarios -> moderacao manual ligada\n";
