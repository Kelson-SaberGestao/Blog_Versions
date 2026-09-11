<?php
/**
 * Post individual.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$qb_cat    = qb_primary_category();
	$qb_author = get_the_author();
	?>

	<div class="wrap post-wrap">

		<nav class="breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'quality-blog' ); ?>">
			<span class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'quality-blog' ); ?></a></span>
			<?php if ( $qb_cat ) : ?>
				<span class="crumb"><a href="<?php echo esc_url( get_term_link( $qb_cat ) ); ?>"><?php echo esc_html( $qb_cat->name ); ?></a></span>
			<?php endif; ?>
			<span class="crumb" aria-current="page"><?php the_title(); ?></span>
		</nav>

		<article <?php post_class( 'post' ); ?>>

			<header class="post-header">
				<?php if ( $qb_cat ) : ?>
					<a class="pill" href="<?php echo esc_url( get_term_link( $qb_cat ) ); ?>"><?php echo esc_html( $qb_cat->name ); ?></a>
				<?php endif; ?>

				<h1><?php the_title(); ?></h1>

				<?php if ( has_excerpt() ) : ?>
					<p class="post-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>

				<div class="post-meta">
					<span class="avatar"><?php echo esc_html( qb_initials( $qb_author ) ); ?></span>
					<div class="post-meta-text">
						<b><?php echo esc_html( $qb_author ); ?></b>
						<span>
							<?php
							printf(
								/* translators: 1: data de publicacao, 2: minutos de leitura */
								esc_html__( 'Published %1$s &middot; %2$d min read', 'quality-blog' ),
								esc_html( get_the_date() ),
								absint( qb_reading_time() )
							);
							?>
						</span>
					</div>

					<div class="post-share">
						<button class="share-btn" type="button" onclick="copyPostLink(this)" aria-label="<?php esc_attr_e( 'Copy link', 'quality-blog' ); ?>">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10 13a5 5 0 007.07 0l2.83-2.83a5 5 0 00-7.07-7.07L11.5 4.5"/><path d="M14 11a5 5 0 00-7.07 0L4.1 13.83a5 5 0 007.07 7.07l1.36-1.36"/></svg>
							<span class="copied-tip"><?php esc_html_e( 'Link copied!', 'quality-blog' ); ?></span>
						</button>

						<a class="share-btn" target="_blank" rel="noopener noreferrer"
							href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode( get_permalink() ); ?>"
							aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'quality-blog' ); ?>">
							<svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.24 8.25h4.5V23H.24V8.25zM8.25 8.25h4.31v2.02h.06c.6-1.14 2.07-2.34 4.26-2.34 4.56 0 5.4 3 5.4 6.9V23h-4.5v-6.6c0-1.57-.03-3.6-2.2-3.6-2.2 0-2.53 1.72-2.53 3.48V23h-4.5V8.25z"/></svg>
						</a>
					</div>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="post-cover" style="background-image:url(<?php echo esc_url( get_the_post_thumbnail_url( null, 'qb-cover' ) ); ?>)"
					role="img" aria-label="<?php the_title_attribute(); ?>"></div>
			<?php endif; ?>

			<div class="post-body">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<div class="page-links">',
						'after'  => '</div>',
					)
				);
				?>
			</div>

			<?php if ( has_tag() ) : ?>
				<div class="post-tags"><?php the_tags( '', '', '' ); ?></div>
			<?php endif; ?>
		</article>

		<?php
		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
		?>
	</div>

	<?php
	/* Relacionados: mesma categoria, sem repetir o post atual. */
	if ( $qb_cat ) :
		$qb_related = new WP_Query(
			array(
				'posts_per_page' => 3,
				'cat'            => $qb_cat->term_id,
				'post__not_in'   => array( get_the_ID() ),
				'no_found_rows'  => true,
			)
		);

		if ( $qb_related->have_posts() ) :
			?>
			<section class="wrap section related-posts">
				<div class="section-head"><h2><?php esc_html_e( 'Related articles', 'quality-blog' ); ?></h2></div>
				<div class="grid-3">
					<?php
					while ( $qb_related->have_posts() ) :
						$qb_related->the_post();
						get_template_part( 'template-parts/card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</section>
			<?php
		endif;
	endif;

endwhile;

get_footer();
