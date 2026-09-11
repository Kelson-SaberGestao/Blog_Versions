<?php
/**
 * Card de post. Aceita $args['variant'] = 'feature' | 'small' | ''.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$qb_variant = isset( $args['variant'] ) ? $args['variant'] : '';
$qb_cat     = qb_primary_category();
$qb_author  = get_the_author();
?>
<a class="card <?php echo esc_attr( $qb_variant ); ?>" href="<?php the_permalink(); ?>">
	<?php qb_card_art( 'small' === $qb_variant ? 'small' : '' ); ?>
	<div class="card-body">
		<?php if ( $qb_cat ) : ?>
			<span class="pill"><?php echo esc_html( $qb_cat->name ); ?></span>
		<?php endif; ?>

		<h3><?php the_title(); ?></h3>

		<?php if ( 'feature' === $qb_variant ) : ?>
			<p class="excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
		<?php endif; ?>

		<div class="meta">
			<span class="avatar"><?php echo esc_html( qb_initials( $qb_author ) ); ?></span>
			<?php echo esc_html( $qb_author ); ?>
			<?php if ( 'small' !== $qb_variant ) : ?>
				&middot; <?php echo esc_html( sprintf( /* translators: %d: minutos */ __( '%d min read', 'quality-blog' ), qb_reading_time() ) ); ?>
			<?php endif; ?>
		</div>
	</div>
</a>
