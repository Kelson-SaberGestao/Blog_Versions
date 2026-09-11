<?php
/**
 * Item dos blocos "Browse by topic".
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<a class="mini-post" href="<?php the_permalink(); ?>">
	<?php qb_mini_art(); ?>
	<h3><?php the_title(); ?></h3>
</a>
