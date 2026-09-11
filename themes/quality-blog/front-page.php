<?php
/**
 * Home: destaque + pilha lateral, ultimos artigos e blocos por tema.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/*
 * Bloco 1 - destaque (1 post) + pilha lateral (4 posts).
 * Guardamos os IDs ja usados para nao repetir post mais abaixo.
 */
$qb_used = array();

$qb_hero = new WP_Query(
	array(
		'posts_per_page'      => 5,
		'ignore_sticky_posts' => false,
		'no_found_rows'       => true,
	)
);
?>

<section class="wrap hero">
	<div class="hero-grid">
		<?php
		$qb_i = 0;
		while ( $qb_hero->have_posts() ) :
			$qb_hero->the_post();
			$qb_used[] = get_the_ID();

			if ( 0 === $qb_i ) {
				get_template_part( 'template-parts/card', null, array( 'variant' => 'feature' ) );
				echo '<div class="side-stack">';
			} else {
				get_template_part( 'template-parts/card', null, array( 'variant' => 'small' ) );
			}
			$qb_i++;
		endwhile;

		if ( $qb_i > 0 ) {
			echo '</div>';
		}
		wp_reset_postdata();
		?>
	</div>
</section>

<?php
/* Bloco 2 - ultimos artigos, 6 posts em duas linhas. */
$qb_latest = new WP_Query(
	array(
		'posts_per_page' => 6,
		'post__not_in'   => $qb_used,
		'no_found_rows'  => true,
	)
);

if ( $qb_latest->have_posts() ) :
	?>
	<section class="wrap section">
		<div class="section-head">
			<h2><?php esc_html_e( 'Latest articles', 'quality-blog' ); ?></h2>
			<a class="see-all" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' ) ); ?>">
				<?php esc_html_e( 'Browse all', 'quality-blog' ); ?>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M9 6l6 6-6 6"/></svg>
			</a>
		</div>

		<div class="grid-3">
			<?php
			while ( $qb_latest->have_posts() ) :
				$qb_latest->the_post();
				$qb_used[] = get_the_ID();
				get_template_part( 'template-parts/card' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</section>
	<?php
endif;

/*
 * Bloco 3 - um bloco por categoria, com os 3 posts mais recentes dela.
 * A ordem segue o mapa de cores; a cor entra como --topic-color.
 */
$qb_terms = get_terms(
	array(
		'taxonomy'   => 'category',
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 9,
		// "Sem categoria" e o deposito do WordPress, nao um tema editorial.
		'exclude'    => array( (int) get_option( 'default_category' ) ),
	)
);

if ( ! is_wp_error( $qb_terms ) && ! empty( $qb_terms ) ) :
	?>
	<section class="wrap section">
		<div class="section-head"><h2><?php esc_html_e( 'Browse by topic', 'quality-blog' ); ?></h2></div>

		<?php foreach ( $qb_terms as $qb_term ) : ?>
			<?php
			$qb_topic = new WP_Query(
				array(
					'posts_per_page' => 3,
					'cat'            => $qb_term->term_id,
					'no_found_rows'  => true,
				)
			);

			if ( ! $qb_topic->have_posts() ) {
				continue;
			}
			?>
			<div class="topic-section" style="--topic-color: <?php echo esc_attr( qb_topic_color( $qb_term ) ); ?>;">
				<div class="topic-heading">
					<div class="topic-heading-left">
						<span class="topic-badge"><?php echo esc_html( $qb_term->name ); ?></span>
					</div>
					<a class="topic-see-all" href="<?php echo esc_url( get_term_link( $qb_term ) ); ?>">
						<?php esc_html_e( 'Browse all', 'quality-blog' ); ?>
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M9 6l6 6-6 6"/></svg>
					</a>
				</div>

				<div class="topic-mini-grid">
					<?php
					while ( $qb_topic->have_posts() ) :
						$qb_topic->the_post();
						get_template_part( 'template-parts/mini-post' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		<?php endforeach; ?>
	</section>
	<?php
endif;

get_footer();
