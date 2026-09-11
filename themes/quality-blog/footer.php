<?php
/**
 * Rodape - cinco colunas, como no prototipo.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$qb_subscribe = get_theme_mod( 'qb_subscribe_url', '' );
?>
</main>

<footer>
	<div class="wrap foot-grid">

		<div class="foot-brand">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" style="margin-bottom:14px;">
				<img class="brand-logo"
					src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-full.png' ) ); ?>"
					alt="Qualiex" width="327" height="88" />
				<div class="brand-divider"></div>
				<div class="brand-name"><span class="brand-thin"><?php echo esc_html_x( 'Quality', 'primeira linha do lettering, menor', 'quality-blog' ); ?></span><b><?php echo esc_html_x( 'Blog', 'segunda linha do lettering, maior e em negrito', 'quality-blog' ); ?></b></div>
			</a>

			<p><?php echo esc_html( qb_mod( 'qb_footer_text' ) ); ?></p>

			<?php
			$qb_social = array_filter(
				qb_social_links(),
				function ( $key ) {
					return '' !== get_theme_mod( $key, '' );
				},
				ARRAY_FILTER_USE_KEY
			);
			?>
			<?php if ( ! empty( $qb_social ) ) : ?>
				<div class="foot-social">
					<?php foreach ( $qb_social as $qb_key => $qb_net ) : ?>
						<a href="<?php echo esc_url( get_theme_mod( $qb_key ) ); ?>"
							target="_blank" rel="noopener noreferrer"
							aria-label="<?php echo esc_attr( $qb_net[0] ); ?>">
							<?php echo $qb_net[1]; // phpcs:ignore WordPress.Security.EscapeOutput -- SVG fixo do tema. ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div>
			<h5><?php esc_html_e( 'Categories', 'quality-blog' ); ?></h5>
			<ul>
				<?php
				wp_list_categories(
					array(
						'title_li'   => '',
						'hide_empty' => true,
						'number'     => 4,
						'orderby'    => 'count',
						'order'      => 'DESC',
					)
				);
				?>
			</ul>
		</div>

		<div>
			<h5><?php esc_html_e( 'Resources', 'quality-blog' ); ?></h5>
			<ul>
				<?php
				if ( has_nav_menu( 'footer-resources' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer-resources',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'depth'          => 1,
						)
					);
				} else {
					// Sem menu montado ainda: mostra as paginas do site.
					wp_list_pages( array( 'title_li' => '', 'number' => 4 ) );
				}
				?>
			</ul>
		</div>

		<div>
			<h5><?php esc_html_e( 'Software', 'quality-blog' ); ?></h5>
			<ul>
				<?php
				if ( has_nav_menu( 'footer-software' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'footer-software',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'depth'          => 1,
						)
					);
				} else {
					printf(
						'<li><a href="%1$s">%2$s</a></li>',
						esc_url( 'https://qualiex.com' ),
						esc_html__( 'Qualiex for Quality', 'quality-blog' )
					);
				}
				?>
			</ul>
		</div>

		<div>
			<h5><?php esc_html_e( 'Stay updated', 'quality-blog' ); ?></h5>
			<p style="font-size:13px;color:var(--muted);margin:0 0 12px;">
				<?php echo esc_html( qb_mod( 'qb_newsletter_text' ) ); ?>
			</p>

			<?php if ( $qb_subscribe ) : ?>
				<form class="foot-newsletter" action="<?php echo esc_url( $qb_subscribe ); ?>" method="get" target="_blank">
					<label class="screen-reader-text" for="qb-foot-email"><?php esc_html_e( 'Your email', 'quality-blog' ); ?></label>
					<input type="email" id="qb-foot-email" name="email" required
						placeholder="<?php esc_attr_e( 'you@company.com', 'quality-blog' ); ?>" />
					<button type="submit"><?php esc_html_e( 'Subscribe', 'quality-blog' ); ?></button>
				</form>
			<?php else : ?>
				<p class="foot-newsletter-todo">
					<?php esc_html_e( 'Newsletter not connected yet — set the destination under Customize → Footer and social.', 'quality-blog' ); ?>
				</p>
			<?php endif; ?>
		</div>

	</div>

	<div class="wrap foot-bottom">
		<span>
			<?php
			printf(
				/* translators: 1: ano, 2: nome do site */
				esc_html__( '© %1$s %2$s. All rights reserved.', 'quality-blog' ),
				esc_html( wp_date( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
		</span>

		<span class="foot-legal">
			<?php
			if ( has_nav_menu( 'footer-legal' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'footer-legal',
						'container'      => false,
						'items_wrap'     => '%3$s',
						'depth'          => 1,
					)
				);
			} elseif ( get_privacy_policy_url() ) {
				printf(
					'<a href="%1$s">%2$s</a>',
					esc_url( get_privacy_policy_url() ),
					esc_html__( 'Privacy Policy', 'quality-blog' )
				);
			}
			?>
		</span>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
