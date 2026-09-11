<?php
/**
 * Quality Blog - funcoes do tema.
 *
 * @package quality-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'QB_VERSION', '0.1.0' );

/**
 * Versao usada no ?ver= do CSS e do JS.
 *
 * Usa a data de modificacao do arquivo, nao QB_VERSION. Com um numero fixo, o
 * navegador continua servindo o CSS antigo depois de instalar um tema novo -
 * e a pessoa jura que a atualizacao nao pegou.
 */
function qb_asset_version( $relative ) {
	$path = get_theme_file_path( $relative );

	return file_exists( $path ) ? (string) filemtime( $path ) : QB_VERSION;
}

/* -------------------------------------------------------------------------
 * Suporte do tema
 * ---------------------------------------------------------------------- */

function qb_setup() {
	// Sem isto o WordPress nem procura os arquivos de traducao.
	load_theme_textdomain( 'quality-blog', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// Recortes usados pelos cards e pela capa do post.
	add_image_size( 'qb-card', 720, 480, true );
	add_image_size( 'qb-cover', 1400, 760, true );

	register_nav_menus(
		array(
			'primary'          => __( 'Menu principal', 'quality-blog' ),
			'mobile'           => __( 'Menu mobile', 'quality-blog' ),
			'footer-resources' => __( 'Rodape - coluna Resources', 'quality-blog' ),
			'footer-software'  => __( 'Rodape - coluna Software', 'quality-blog' ),
			'footer-legal'     => __( 'Rodape - linha final (privacidade, cookies)', 'quality-blog' ),
		)
	);
}
add_action( 'after_setup_theme', 'qb_setup' );

/* -------------------------------------------------------------------------
 * CSS e JS
 * ---------------------------------------------------------------------- */

function qb_assets() {
	wp_enqueue_style(
		'qb-fonts',
		'https://fonts.googleapis.com/css2?family=Roboto:wght@500;700;900&family=Inter:wght@400;500;600&family=Nunito:wght@600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style( 'qb-style', get_stylesheet_uri(), array( 'qb-fonts' ), qb_asset_version( 'style.css' ) );

	wp_enqueue_script( 'qb-main', get_theme_file_uri( 'assets/js/main.js' ), array(), qb_asset_version( 'assets/js/main.js' ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'qb_assets' );

/* -------------------------------------------------------------------------
 * Cor por categoria
 *
 * O WordPress nao tem cor de categoria nativa. O mapa abaixo veio direto do
 * design; a chave e o slug da categoria. Categoria sem entrada aqui usa o
 * azul da marca. Para mudar uma cor, edite este mapa - nao o CSS.
 * ---------------------------------------------------------------------- */

function qb_topic_colors() {
	return apply_filters(
		'qb_topic_colors',
		array(
			'quality-management-systems' => '#0544AB',
			'quality-tools'              => '#0E8F8F',
			'continuous-improvement'     => '#16A34A',
			'organizational-culture'     => '#C026A3',
			'project-management'         => '#7C3AED',
			'business-strategy'          => '#D4841A',
			'quality-gurus'              => '#1E3A8A',
			'process-management'         => '#0369A1',
			'philosophy-of-excellence'   => '#B91C1C',

			// Espanhol: mesmas cores, para o par visual sobreviver a traducao.
			// Os slugs abaixo sao os reais do blogdelacalidad.com - varios
			// terminam em -es por causa da migracao, entao nao da para
			// derivar do nome.
			'sistema-de-gestion'         => '#0544AB',
			'herramientas-de-la-calidad' => '#0E8F8F',
			'mejora-continua'            => '#16A34A',
			'cultura-organizacional-es'  => '#C026A3',
			'gestion-de-procesos'        => '#0369A1',
			'estrategia-empresarial-es'  => '#D4841A',
			'clientes-es'                => '#1E3A8A',
			'columnistas-es'             => '#7C3AED',
			'ultimas-es'                 => '#B91C1C',
		)
	);
}

function qb_topic_color( $term ) {
	static $assigned = null;

	$colors = qb_topic_colors();
	$slug   = is_object( $term ) ? $term->slug : (string) $term;

	if ( isset( $colors[ $slug ] ) ) {
		return $colors[ $slug ];
	}

	/*
	 * Categoria fora do mapa - renomeada, nova, ou de um site que ja existia
	 * com os slugs dele. Em vez de devolver sempre o mesmo azul, distribui as
	 * cores que sobraram da paleta, uma por categoria.
	 *
	 * Um hash simples do slug era mais curto, mas colidia: duas categorias
	 * caiam na mesma cor e o codigo por cor deixava de distinguir.
	 */
	if ( null === $assigned ) {
		$assigned = array();
		$palette  = array_values( array_unique( $colors ) );
		$slugs    = get_terms(
			array( 'taxonomy' => 'category', 'hide_empty' => false, 'fields' => 'slugs' )
		);
		$slugs    = is_wp_error( $slugs ) ? array() : $slugs;

		// Cores ja tomadas pelas categorias que estao no mapa.
		$taken = array();
		foreach ( $slugs as $s ) {
			if ( isset( $colors[ $s ] ) ) {
				$taken[] = $colors[ $s ];
			}
		}

		$free = array_values( array_diff( $palette, $taken ) );
		if ( empty( $free ) ) {
			$free = $palette;
		}

		$i = 0;
		foreach ( $slugs as $s ) {
			if ( isset( $colors[ $s ] ) ) {
				continue;
			}
			$assigned[ $s ] = $free[ $i % count( $free ) ];
			$i++;
		}
	}

	return isset( $assigned[ $slug ] ) ? $assigned[ $slug ] : '#0544AB';
}

/**
 * Textos padrao do banner e do rodape.
 *
 * Ficam aqui e em nenhum outro lugar: antes estavam duplicados entre o
 * Customizer e os templates, e so a copia do Customizer era traduzivel - o
 * que fazia o banner sair em ingles na build em espanhol.
 */
function qb_default( $key ) {
	$defaults = array(
		'qb_banner_badge'    => __( 'Free guide', 'quality-blog' ),
		'qb_banner_title'    => __( 'New to ISO 9001?', 'quality-blog' ),
		'qb_banner_sub'      => __( 'The complete clause-by-clause guide - free, no signup walls.', 'quality-blog' ),
		'qb_banner_cta'      => __( 'Get the complete guide', 'quality-blog' ),
		'qb_banner_url'      => '#',
		'qb_footer_text'     => __( 'Independent, practical writing on quality management, ISO 9001 and continuous improvement - published by the team behind Qualiex.', 'quality-blog' ),
		'qb_newsletter_text' => __( 'One email a week, new articles and free resources.', 'quality-blog' ),
	);

	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Valor configurado, ou o padrao traduzido.
 */
function qb_mod( $key ) {
	return get_theme_mod( $key, qb_default( $key ) );
}

/* -------------------------------------------------------------------------
 * Helpers de conteudo
 * ---------------------------------------------------------------------- */

/**
 * Categoria principal do post (a primeira). Devolve null se nao houver.
 */
function qb_primary_category( $post_id = null ) {
	$cats = get_the_category( $post_id );

	return ! empty( $cats ) ? $cats[0] : null;
}

/**
 * Tempo de leitura em minutos, a 200 palavras por minuto.
 */
function qb_reading_time( $post_id = null ) {
	$content = get_post_field( 'post_content', $post_id ? $post_id : get_the_ID() );
	$words   = preg_match_all( '/\p{L}+/u', wp_strip_all_tags( strip_shortcodes( $content ) ) );

	return max( 1, (int) round( $words / 200 ) );
}

/**
 * Iniciais do autor, usadas no avatar redondo dos cards.
 */
function qb_initials( $name ) {
	$out   = '';
	$parts = preg_split( '/\s+/u', trim( (string) $name ) );

	foreach ( $parts as $part ) {
		if ( '' === $part ) {
			continue;
		}
		$out .= mb_strtoupper( mb_substr( $part, 0, 1 ) );
		if ( mb_strlen( $out ) >= 2 ) {
			break;
		}
	}

	return '' !== $out ? $out : '?';
}

/**
 * Imprime o bloco de arte de um card.
 *
 * Usa a imagem destacada do post. Sem imagem destacada, cai numa das fotos
 * que vieram do prototipo, escolhida de forma estavel pelo ID - assim o card
 * nunca aparece vazio e o mesmo post mantem sempre a mesma foto.
 *
 * @param string $classes Classes extras, por exemplo 'small'.
 * @param string $size    Recorte a usar.
 */
function qb_card_art( $classes = '', $size = 'qb-card' ) {
	$fallbacks = array(
		'photo-doc',
		'photo-search',
		'photo-shield',
		'photo-target',
		'photo-bulb',
		'photo-alert',
		'photo-book',
		'photo-briefcase',
		'photo-trophy',
		'photo-gear',
		'photo-flow',
		'photo-scale',
		'photo-people',
		'photo-chart',
		'photo-wrench',
	);

	if ( has_post_thumbnail() ) {
		printf(
			'<div class="art %1$s" style="background-image:url(%2$s)" role="img" aria-label="%3$s"></div>',
			esc_attr( $classes ),
			esc_url( get_the_post_thumbnail_url( null, $size ) ),
			esc_attr( get_the_title() )
		);

		return;
	}

	$fallback = $fallbacks[ absint( get_the_ID() ) % count( $fallbacks ) ];

	printf(
		'<div class="art %1$s %2$s" aria-hidden="true"></div>',
		esc_attr( $classes ),
		esc_attr( $fallback )
	);
}

/**
 * Mesma ideia, para a arte menor dos blocos "Browse by topic".
 */
function qb_mini_art() {
	if ( has_post_thumbnail() ) {
		printf(
			'<div class="mini-post-art" style="background-image:url(%1$s)" role="img" aria-label="%2$s"></div>',
			esc_url( get_the_post_thumbnail_url( null, 'qb-card' ) ),
			esc_attr( get_the_title() )
		);

		return;
	}

	qb_card_art( 'mini-post-art' );
}

/* -------------------------------------------------------------------------
 * Menu principal
 *
 * Reaproveita a marcacao do prototipo: item com filhos vira botao com
 * dropdown; item sem filhos vira link simples na mesma linha.
 * ---------------------------------------------------------------------- */

class QB_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= '<div class="dropdown-panel cols-2">';
		}
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 === $depth ) {
			$output .= '</div>';
		}
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$url      = esc_url( $item->url );
		$title    = esc_html( $item->title );
		$extra    = implode( ' ', array_filter( (array) $item->classes, 'strlen' ) );
		$has_kids = in_array( 'menu-item-has-children', (array) $item->classes, true );

		if ( $depth > 0 ) {
			$output .= '<a href="' . $url . '">' . $title . '</a>';
			return;
		}

		if ( $has_kids ) {
			$output .= '<div class="nav-item ' . esc_attr( $extra ) . '">';
			$output .= '<button class="nav-trigger" type="button" aria-expanded="false">' . $title;
			$output .= '<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 9l6 6 6-6"/></svg>';
			$output .= '</button>';
			return;
		}

		// Item solto: adicione a classe "qa-link" no menu do admin para o
		// destaque azul do Quality Assistant.
		if ( false !== strpos( $extra, 'qa-link' ) ) {
			$output .= '<a class="qa-link" href="' . $url . '">'
				. '<span class="qa-short"><span class="qa-highlight">' . $title . '</span></span>'
				. '<span class="qa-arrow">&rarr;</span></a>';
			return;
		}

		$output .= '<div class="nav-item"><a class="nav-trigger" href="' . $url . '">' . $title . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 === $depth && false === strpos( implode( ' ', (array) $item->classes ), 'qa-link' ) ) {
			$output .= '</div>';
		}
	}
}

/**
 * Menu principal, com as categorias do blog como alternativa se nenhum menu
 * tiver sido montado ainda no admin.
 */
function qb_primary_menu() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '%3$s',
				'depth'          => 2,
				'walker'         => new QB_Nav_Walker(),
			)
		);

		return;
	}

	echo '<div class="nav-item"><button class="nav-trigger" type="button" aria-expanded="false">'
		. esc_html__( 'Categories', 'quality-blog' )
		. '<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 9l6 6 6-6"/></svg>'
		. '</button><div class="dropdown-panel cols-2">';

	wp_list_categories(
		array(
			'title_li'   => '',
			'hide_empty' => false,
			'walker'     => new QB_Category_Links_Walker(),
		)
	);

	echo '</div></div>';
}

/**
 * Lista de categorias sem <li>, para caber no dropdown.
 */
class QB_Category_Links_Walker extends Walker_Category {

	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$output .= '<a href="' . esc_url( get_term_link( $category ) ) . '">' . esc_html( $category->name ) . '</a>';
	}

	public function end_el( &$output, $category, $depth = 0, $args = array() ) {}

	public function start_lvl( &$output, $depth = 0, $args = array() ) {}

	public function end_lvl( &$output, $depth = 0, $args = array() ) {}
}

/* -------------------------------------------------------------------------
 * Personalizador: banner do topo da home
 * ---------------------------------------------------------------------- */

function qb_customize( $wp_customize ) {
	$wp_customize->add_section(
		'qb_banner',
		array(
			'title'       => __( 'Banner do topo', 'quality-blog' ),
			'priority'    => 30,
			'description' => __( 'Bloco promocional ao lado da marca, na home.', 'quality-blog' ),
		)
	);

	$fields = array(
		'qb_banner_badge' => array( __( 'Selo', 'quality-blog' ), qb_default( 'qb_banner_badge' ) ),
		'qb_banner_title' => array( __( 'Titulo', 'quality-blog' ), qb_default( 'qb_banner_title' ) ),
		'qb_banner_sub'   => array( __( 'Linha de apoio (mobile)', 'quality-blog' ), qb_default( 'qb_banner_sub' ) ),
		'qb_banner_cta'   => array( __( 'Texto do link', 'quality-blog' ), qb_default( 'qb_banner_cta' ) ),
		'qb_banner_url'   => array( __( 'Destino', 'quality-blog' ), '#' ),
	);

	foreach ( $fields as $key => $conf ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $conf[1],
				'sanitize_callback' => ( 'qb_banner_url' === $key ) ? 'esc_url_raw' : 'sanitize_text_field',
			)
		);
		$wp_customize->add_control( $key, array( 'label' => $conf[0], 'section' => 'qb_banner', 'type' => 'text' ) );
	}

	$wp_customize->add_setting( 'qb_banner_enabled', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean' ) );
	$wp_customize->add_control(
		'qb_banner_enabled',
		array( 'label' => __( 'Mostrar o banner', 'quality-blog' ), 'section' => 'qb_banner', 'type' => 'checkbox' )
	);

	/* ---- Rodape e redes ---- */

	$wp_customize->add_section(
		'qb_footer',
		array(
			'title'       => __( 'Rodape e redes', 'quality-blog' ),
			'priority'    => 31,
			'description' => __( 'Texto da marca, redes sociais e o botao Subscribe do topo.', 'quality-blog' ),
		)
	);

	$footer_fields = array(
		'qb_footer_text' => array(
			__( 'Texto abaixo da marca', 'quality-blog' ),
			qb_default( 'qb_footer_text' ),
			'sanitize_textarea_field',
			'textarea',
		),
		'qb_newsletter_text' => array(
			__( 'Chamada da newsletter', 'quality-blog' ),
			qb_default( 'qb_newsletter_text' ),
			'sanitize_text_field',
			'text',
		),
		'qb_subscribe_url' => array(
			__( 'Destino do Subscribe / newsletter', 'quality-blog' ),
			'',
			'esc_url_raw',
			'url',
		),
		'qb_social_linkedin'  => array( __( 'LinkedIn', 'quality-blog' ), '', 'esc_url_raw', 'url' ),
		'qb_social_instagram' => array( __( 'Instagram', 'quality-blog' ), '', 'esc_url_raw', 'url' ),
		'qb_social_youtube'   => array( __( 'YouTube', 'quality-blog' ), '', 'esc_url_raw', 'url' ),
	);

	foreach ( $footer_fields as $key => $conf ) {
		$wp_customize->add_setting( $key, array( 'default' => $conf[1], 'sanitize_callback' => $conf[2] ) );
		$wp_customize->add_control( $key, array( 'label' => $conf[0], 'section' => 'qb_footer', 'type' => $conf[3] ) );
	}
}
add_action( 'customize_register', 'qb_customize' );

/**
 * Icones das redes. Uma rede sem URL configurada simplesmente nao aparece,
 * em vez de virar um link morto para "#".
 */
function qb_social_links() {
	return array(
		'qb_social_instagram' => array(
			'Instagram',
			'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
		),
		'qb_social_linkedin'  => array(
			'LinkedIn',
			'<svg viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1s2.48 1.12 2.48 2.5zM.24 8.25h4.5V23H.24V8.25zM8.25 8.25h4.31v2.02h.06c.6-1.14 2.07-2.34 4.26-2.34 4.56 0 5.4 3 5.4 6.9V23h-4.5v-6.6c0-1.57-.03-3.6-2.2-3.6-2.2 0-2.53 1.72-2.53 3.48V23h-4.5V8.25z"/></svg>',
		),
		'qb_social_youtube'   => array(
			'YouTube',
			'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1.5" y="5.5" width="21" height="13" rx="4"/><path d="M10 9.2l5 2.8-5 2.8V9.2z" fill="currentColor" stroke="none"/></svg>',
		),
	);
}

/* -------------------------------------------------------------------------
 * Comentarios
 * ---------------------------------------------------------------------- */

function qb_comment( $comment, $args, $depth ) {
	// As classes abaixo sao as mesmas do prototipo, para o CSS valer sem retoque.
	?>
	<div id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment' ); ?>>
		<span class="avatar" style="width:36px;height:36px;font-size:12px;"><?php echo esc_html( qb_initials( get_comment_author() ) ); ?></span>
		<div class="comment-body">
			<div class="comment-head">
				<b><?php comment_author(); ?></b>
				<span>
					<?php
					printf(
						/* translators: %s: tempo desde o comentario, ex. "3 days" */
						esc_html__( '%s ago', 'quality-blog' ),
						esc_html( human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) )
					);
					?>
				</span>
			</div>

			<?php if ( '0' === $comment->comment_approved ) : ?>
				<p class="comment-pending"><em><?php esc_html_e( 'Your comment is awaiting moderation.', 'quality-blog' ); ?></em></p>
			<?php endif; ?>

			<?php comment_text(); ?>

			<?php
			comment_reply_link(
				array_merge(
					$args,
					array(
						'depth'      => $depth,
						'max_depth'  => $args['max_depth'],
						'reply_text' => __( 'Reply', 'quality-blog' ),
					)
				)
			);
			?>
		</div>
	<?php
	// O </div> de fechamento e responsabilidade do wp_list_comments.
}
