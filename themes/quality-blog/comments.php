<?php
/**
 * Comentarios.
 *
 * Usa o sistema nativo do WordPress: a fila de moderacao, o antispam e o
 * controle de quem pode comentar ficam todos no admin, em Configuracoes >
 * Discussao. Nada disso e do tema.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Nao mostra nada se o post estiver protegido por senha e ela nao foi dada.
if ( post_password_required() ) {
	return;
}
?>

<section class="comments-section" id="comments">
	<h2>
		<?php
		$qb_count = get_comments_number();

		if ( 0 === (int) $qb_count ) {
			esc_html_e( 'Comments', 'quality-blog' );
		} else {
			printf(
				/* translators: %s: numero de comentarios */
				esc_html( _n( '%s comment', '%s comments', $qb_count, 'quality-blog' ) ),
				esc_html( number_format_i18n( $qb_count ) )
			);
		}
		?>
	</h2>

	<?php if ( have_comments() ) : ?>
		<div class="comment-list">
			<?php
			wp_list_comments(
				array(
					'callback'    => 'qb_comment',
					'style'       => 'div',
					'short_ping'  => true,
					'avatar_size' => 0,
				)
			);
			?>
		</div>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => esc_html__( 'Previous', 'quality-blog' ),
				'next_text' => esc_html__( 'Next', 'quality-blog' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-results"><?php esc_html_e( 'Comments are closed on this post.', 'quality-blog' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'         => 'comment-form',
			'title_reply'        => esc_html__( 'Leave a comment', 'quality-blog' ),
			'title_reply_to'     => esc_html__( 'Reply to %s', 'quality-blog' ),
			'cancel_reply_link'  => esc_html__( 'Cancel', 'quality-blog' ),
			'label_submit'       => esc_html__( 'Post comment', 'quality-blog' ),
			'comment_notes_before' => '',
			'comment_field'      => sprintf(
				'<p class="comment-form-comment"><label class="screen-reader-text" for="comment">%1$s</label>' .
				'<textarea id="comment" name="comment" rows="4" required placeholder="%2$s"></textarea></p>',
				esc_html__( 'Comment', 'quality-blog' ),
				esc_attr__( 'Share your experience…', 'quality-blog' )
			),
			'fields'             => array(
				'author' => sprintf(
					'<div class="comment-form-row"><p class="comment-form-author">' .
					'<label class="screen-reader-text" for="author">%1$s</label>' .
					'<input id="author" name="author" type="text" required placeholder="%1$s" value="%2$s" /></p>',
					esc_attr__( 'Your name', 'quality-blog' ),
					esc_attr( wp_get_current_commenter()['comment_author'] )
				),
				'email'  => sprintf(
					'<p class="comment-form-email">' .
					'<label class="screen-reader-text" for="email">%1$s</label>' .
					'<input id="email" name="email" type="email" required placeholder="%1$s" value="%2$s" /></p></div>',
					esc_attr__( 'Your email', 'quality-blog' ),
					esc_attr( wp_get_current_commenter()['comment_author_email'] )
				),
			),
		)
	);
	?>
</section>
