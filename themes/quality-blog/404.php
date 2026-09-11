<?php
/**
 * Pagina nao encontrada.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<section class="wrap section">
	<div class="section-head"><h2><?php esc_html_e( 'Page not found', 'quality-blog' ); ?></h2></div>
	<p class="no-results">
		<?php esc_html_e( 'This page does not exist, or it has moved. Try a search, or head back to the homepage.', 'quality-blog' ); ?>
	</p>
	<?php get_search_form(); ?>
	<p><a class="see-all" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the homepage', 'quality-blog' ); ?></a></p>
</section>

<?php
get_footer();
