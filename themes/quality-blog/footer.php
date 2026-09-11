<?php
/**
 * Rodape.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>

<footer>
	<div class="wrap foot-grid">
		<div class="foot-brand">
			<div class="brand" style="margin-bottom:14px;">
				<img class="brand-logo"
					src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-full.png' ) ); ?>"
					alt="Qualiex" width="327" height="88" />
			</div>
			<p><?php bloginfo( 'description' ); ?></p>
		</div>

		<div>
			<h5><?php esc_html_e( 'Categories', 'quality-blog' ); ?></h5>
			<ul>
				<?php
				wp_list_categories(
					array(
						'title_li'   => '',
						'hide_empty' => true,
						'number'     => 9,
					)
				);
				?>
			</ul>
		</div>

		<div>
			<h5><?php esc_html_e( 'Blog', 'quality-blog' ); ?></h5>
			<ul>
				<?php
				if ( has_nav_menu( 'footer' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'depth'          => 1,
						)
					);
				} else {
					wp_list_pages( array( 'title_li' => '' ) );
				}
				?>
			</ul>
		</div>
	</div>

	<div class="wrap foot-bottom">
		<span>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
		<span><?php esc_html_e( 'Published by the team behind Qualiex', 'quality-blog' ); ?></span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
