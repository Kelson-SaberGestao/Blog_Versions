<?php
/**
 * Busca do cabecalho. Envia para a busca nativa do WordPress.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$qb_id = 'qb-search-' . wp_unique_id();
?>
<form class="header-search-lg" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
	<label class="screen-reader-text" for="<?php echo esc_attr( $qb_id ); ?>"><?php esc_html_e( 'Search', 'quality-blog' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $qb_id ); ?>" name="s"
		placeholder="<?php esc_attr_e( 'Search articles, tools, guides…', 'quality-blog' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>" />
</form>
