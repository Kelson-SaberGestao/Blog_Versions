<?php
/**
 * Cabecalho: barra fixa em todas as paginas, masthead so na home.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'quality-blog' ); ?></a>

<header class="main">
	<nav class="primary-row">
		<div class="wrap nav-row-2">
			<a class="nav-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<img class="nav-brand-logo"
					src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-mark.png' ) ); ?>"
					alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
					width="30" height="30" />
			</a>

			<div class="nav-links">
				<?php qb_primary_menu(); ?>
			</div>

			<div class="nav-actions">
				<?php get_search_form(); ?>

				<?php
				$qb_subscribe = get_theme_mod( 'qb_subscribe_url', '#' );
				?>
				<a class="subscribe-btn" href="<?php echo esc_url( $qb_subscribe ); ?>"><?php esc_html_e( 'Subscribe', 'quality-blog' ); ?></a>

				<button class="hamburger-btn" id="hamburgerBtn" type="button"
					aria-label="<?php esc_attr_e( 'Open menu', 'quality-blog' ); ?>"
					aria-controls="mobileMenu" aria-expanded="false"
					onclick="toggleMobileMenu()">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
				</button>
			</div>
		</div>
	</nav>

	<?php if ( is_front_page() ) : ?>
		<div class="wrap masthead">
			<a class="masthead-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<img class="masthead-logo"
					src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-full.png' ) ); ?>"
					alt="Qualiex" width="327" height="88" />
				<span class="masthead-divider"></span>
				<span class="masthead-wordmark"><span>Quality</span><b>Blog</b></span>
			</a>

			<?php if ( get_theme_mod( 'qb_banner_enabled', true ) ) : ?>
				<a class="masthead-banner" href="<?php echo esc_url( get_theme_mod( 'qb_banner_url', '#' ) ); ?>">
					<div class="mb-copy">
						<span class="mb-badge"><?php echo esc_html( get_theme_mod( 'qb_banner_badge', 'Free guide' ) ); ?></span>
						<h3><?php echo esc_html( get_theme_mod( 'qb_banner_title', 'New to ISO 9001?' ) ); ?></h3>
						<span class="mb-sub"><?php echo esc_html( get_theme_mod( 'qb_banner_sub', 'The complete clause-by-clause guide - free, no signup walls.' ) ); ?></span>
						<span class="mb-cta"><?php echo esc_html( get_theme_mod( 'qb_banner_cta', 'Get the complete guide' ) ); ?> &rarr;</span>
					</div>
					<span class="mb-arrow" aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</header>

<div class="mobile-menu-backdrop" id="mobileMenuBackdrop" onclick="toggleMobileMenu()"></div>

<nav class="mobile-menu" id="mobileMenu" aria-label="<?php esc_attr_e( 'Mobile menu', 'quality-blog' ); ?>">
	<div class="mobile-menu-head">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img class="brand-logo"
				src="<?php echo esc_url( get_theme_file_uri( 'assets/img/logo-full.png' ) ); ?>"
				alt="Qualiex" width="327" height="88" />
		</a>
		<button class="mobile-menu-close" type="button"
			aria-label="<?php esc_attr_e( 'Close menu', 'quality-blog' ); ?>"
			onclick="toggleMobileMenu()">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
		</button>
	</div>

	<form class="mobile-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
		<label class="screen-reader-text" for="mobileSearchInput"><?php esc_html_e( 'Search', 'quality-blog' ); ?></label>
		<input type="search" id="mobileSearchInput" name="s"
			placeholder="<?php esc_attr_e( 'Search articles, tools, guides…', 'quality-blog' ); ?>"
			value="<?php echo esc_attr( get_search_query() ); ?>" />
	</form>

	<div class="mobile-nav">
		<?php
		if ( has_nav_menu( 'mobile' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'mobile',
					'container'      => false,
					'items_wrap'     => '%3$s',
					'depth'          => 2,
					'walker'         => new QB_Nav_Walker(),
				)
			);
		} else {
			qb_primary_menu();
		}
		?>
	</div>
</nav>

<main id="content">
