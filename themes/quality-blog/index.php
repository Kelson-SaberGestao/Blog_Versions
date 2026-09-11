<?php
/**
 * Listagem padrao (blog, arquivo, categoria, tag, busca).
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$qb_color = is_category() ? qb_topic_color( get_queried_object() ) : '#0544AB';
?>

<section class="wrap section" style="--topic-color: <?php echo esc_attr( $qb_color ); ?>;">
	<div class="section-head">
		<h2>
			<?php
			if ( is_search() ) {
				/* translators: %s: termo buscado */
				printf( esc_html__( 'Results for "%s"', 'quality-blog' ), esc_html( get_search_query() ) );
			} elseif ( is_category() || is_tag() || is_tax() ) {
				single_term_title();
			} elseif ( is_author() ) {
				the_archive_title();
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				esc_html_e( 'All blog posts', 'quality-blog' );
			}
			?>
		</h2>
	</div>

	<?php if ( is_category() || is_tag() || is_tax() ) : ?>
		<?php $qb_desc = term_description(); ?>
		<?php if ( $qb_desc ) : ?>
			<div class="archive-intro"><?php echo wp_kses_post( $qb_desc ); ?></div>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="grid-3">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/card' );
			endwhile;
			?>
		</div>

		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => esc_html__( 'Previous', 'quality-blog' ),
				'next_text' => esc_html__( 'Next', 'quality-blog' ),
			)
		);
		?>
	<?php else : ?>
		<p class="no-results">
			<?php esc_html_e( 'No posts found. Try a different search term.', 'quality-blog' ); ?>
		</p>
		<?php get_search_form(); ?>
	<?php endif; ?>
</section>

<?php
get_footer();
