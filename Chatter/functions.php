<?php if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * @package Chatter
 */
require_once( get_template_directory() . '/inc/utils.php' );
Chatter_maybe_define( 'Chatter_INC_PATH', get_template_directory()     . '/inc' );
Chatter_maybe_define( 'Chatter_INC_URL',  get_template_directory_uri() . '/inc' );
Chatter_maybe_define( 'Chatter_JS_PATH',  get_template_directory()     . '/js'  );
Chatter_maybe_define( 'Chatter_JS_URL',   get_template_directory_uri() . '/js'  );

class Chatter {
	/**
	 * DB version.
	 *
	 * @var int
	 */
	var $db_version = 3;

	/**
	 * Options.
	 *
	 * @var array
	 */
	var $options = array();

	/**
	 * Option name in DB.
	 *
	 * @var string
	 */
	var $option_name = 'Chatter_manager';

	/**
	 * Components.
	 *
	 * @var array
	 */
	var $components = array();

	/**
	 * Includes and instantiates the various Chatter components.
	 */
	function Chatter() {
		// Fetch options
		$this->options = get_option( $this->option_name );
		if ( false === $this->options )
			$this->options = array();
		// Include the Chatter components
		$includes = array( 'compat', 'terms-in-comments', 'js-locale',
			'mentions', 'search', 'js', 'options-page', 'widgets/recent-tags', 'widgets/recent-comments',
			'list-creator' );
		require_once( Chatter_INC_PATH . "/template-tags.php" );
		// Logged-out/unprivileged users use the add_feed() + ::ajax_read() API rather than the /admin-ajax.php API
		// current_user_can( 'read' ) should be equivalent to is_user_member_of_blog()
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && ( Chatter_user_can_post() || current_user_can( 'read' ) ) )
			$includes[] = 'ajax';
		foreach ( $includes as $name ) {
			require_once( Chatter_INC_PATH . "/$name.php" );
		}
		// Add the default Chatter components
		$this->add( 'mentions',             'Chatter_Mentions'             );
		$this->add( 'search',               'Chatter_Search'               );
		$this->add( 'post-list-creator',    'Chatter_Post_List_Creator'    );
		$this->add( 'comment-list-creator', 'Chatter_Comment_List_Creator' );
		// Bind actions
		add_action( 'init',       array( &$this, 'init'             ) );
		add_action( 'admin_init', array( &$this, 'maybe_upgrade_db' ), 5 );
	}
	function init() {
		// Load language pack
		load_theme_textdomain( 'Chatter', get_template_directory() . '/languages' );
		// Set up the AJAX read handler
		add_feed( 'Chatter.ajax', array( $this, 'ajax_read' ) );
	}
	function ajax_read() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}
		require_once( Chatter_INC_PATH . '/ajax-read.php' );
		ChatterAjax_Read::dispatch();
	}
	/**
	 * Will upgrade the database if necessary.
	 *
	 * When upgrading, triggers actions:
	 *    'Chatter_upgrade_db_version'
	 *    'Chatter_upgrade_db_version_$number'
	 *
	 * Flushes rewrite rules automatically on upgrade.
	 */
	function maybe_upgrade_db() {
		if ( ! isset( $this->options['db_version'] ) || $this->options['db_version'] < $this->db_version ) {
			$current_db_version = isset( $this->options['db_version'] ) ? $this->options['db_version'] : 0;
			do_action( 'Chatter_upgrade_db_version', $current_db_version );
			for ( ; $current_db_version <= $this->db_version; $current_db_version++ ) {
				do_action( "Chatter_upgrade_db_version_$current_db_version" );
			}
			// Flush rewrite rules once, so callbacks don't have to.
			flush_rewrite_rules();
			$this->set_option( 'db_version', $this->db_version );
			$this->save_options();
		}
	}

	/**
	 * COMPONENTS API
	 */
	function add( $component, $class ) {
		$class = apply_filters( "Chatter_add_component_$component", $class );
		if ( class_exists( $class ) )
			$this->components[ $component ] = new $class();
	}
	function get( $component ) {
		return $this->components[ $component ];
	}
	function remove( $component ) {
		unset( $this->components[ $component ] );
	}
	/**
	 * OPTIONS API
	 */
	function get_option( $key ) {
		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : null;
	}
	function set_option( $key, $value ) {
		return $this->options[ $key ] = $value;
	}
	function save_options() {
		update_option( $this->option_name, $this->options );
	}
}
$GLOBALS['Chatter'] = new Chatter;
function Chatter_get( $component = '' ) {
	global $Chatter;
	return empty( $component ) ? $Chatter : $Chatter->get( $component );
}
function Chatter_get_option( $key ) {
	return $GLOBALS['Chatter']->get_option( $key );
}
function Chatter_set_option( $key, $value ) {
	return $GLOBALS['Chatter']->set_option( $key, $value );
}
function Chatter_save_options() {
	return $GLOBALS['Chatter']->save_options();
}
if ( ! isset( $content_width ) )
	$content_width = 632;
$themecolors = array(
	'bg'     => 'ffffff',
	'text'   => '555555',
	'link'   => '3478e3',
	'border' => 'f1f1f1',
	'url'    => 'd54e21',
);

/**
 * Setup Chatter Theme.
 *
 * Hooks into the after_setup_theme action.
 *
 * @uses Chatter_get_supported_post_formats()
 */
function Chatter_setup() {
	require_once( get_template_directory() . '/inc/custom-header.php' );
	Chatter_setup_custom_header();
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-formats', Chatter_get_supported_post_formats( 'post-format' ) );
	add_theme_support( 'custom-background', apply_filters( 'Chatter_custom_background_args', array( 'default-color' => 'f1f1f1' ) ) );
	add_filter( 'the_content', 'make_clickable', 12 ); // Run later to avoid shortcode conflicts
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'Chatter' ),
	) );

	if ( is_admin() && false === get_option( 'prologue_show_titles' ) )
		add_option( 'prologue_show_titles', 1 );
}
add_filter( 'after_setup_theme', 'Chatter_setup' );

/**
 * Add viewport meta
 */
add_action( 'wp_head', 'Chatter_viewport_meta' );
function Chatter_viewport_meta() {
?>
	<!--  Mobile viewport scale | Disable user zooming as the layout is optimised -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<?php
}

/**
 * Add js to the frontend
 */
add_action( 'wp_enqueue_scripts', 'Chatter_scripts', 999 );
function Chatter_scripts() {
	wp_enqueue_script( 'woo-Chatter-script', get_template_directory_uri() . '/js/script.min.js', array( 'jquery' ), '', true );
	wp_enqueue_script( 'fitvids', get_template_directory() . '/js/fitvids.js', array( 'jquery' ), '', true );
	wp_dequeue_script( 'iphone' );
}

function Chatter_new_nav_menu_items( $items, $args ) {
	if ( $args->theme_location == 'primary' ) {
		$homelink 	= the_widget( 'WP_Widget_Search' );
		$items 		= $items . $homelink;
	}
	return $items;
}
add_filter( 'wp_nav_menu_items', 'Chatter_new_nav_menu_items', 10, 2 );

function Chatter_register_sidebar_right() {
	register_sidebar( array(
		'name' => __( 'Right Sidebar', 'Chatter' ),
		'id'   => 'right-sidebar',
		'description'   => '',
                'class'         => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>' 
	) );
}
add_action( 'widgets_init', 'Chatter_register_sidebar_right' );


function Chatter_register_sidebar_left() {
	register_sidebar( array(
		'name'          => __( 'Left Sidebar', 'Chatter' ),
		'id'            => 'left-sidebar',
		'description'   => '',
                'class'         => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>' )
	);
}
add_action( 'widgets_init', 'Chatter_register_sidebar_left' );


function Chatter_background_color() {
	$background_color = get_option( 'Chatter_background_color' );

	if ( '' != $background_color ) :
	?>
	<style type="text/css">
		body {
			background-color: <?php echo esc_attr( $background_color ); ?>;
		}
	</style>
	<?php endif;
}
add_action( 'wp_head', 'Chatter_background_color' );

function Chatter_background_image() {
	$Chatter_background_image = get_option( 'Chatter_background_image' );

	if ( 'none' == $Chatter_background_image || '' == $Chatter_background_image )
		return false;

?>
	<style type="text/css">
		body {
			background-image: url( <?php echo get_template_directory_uri() . '/i/backgrounds/pattern-' . sanitize_key( $Chatter_background_image ) . '.png' ?> );
		}
	</style>
<?php
}
add_action( 'wp_head', 'Chatter_background_image' );
/*
 *
 * @since Chatter 1.5
 */
function Chatter_body_class_background_image( $classes ) {
	$image = get_option( 'Chatter_background_image' );

	if ( empty( $image ) || 'none' == $image )
		return $classes;

	$classes[] = esc_attr( 'Chatter-background-image-' . $image );

	return $classes;
}
add_action( 'body_class', 'Chatter_body_class_background_image' );

// Content Filters
function Chatter_title( $before = '<h2>', $after = '</h2>', $echo = true ) {
	if ( is_page() )
		return;

	if ( is_single() && false === Chatter_the_title( '', '', false ) ) { ?>
		<h2 class="transparent-title"><?php the_title(); ?></h2><?php
		return true;
	} else {
		Chatter_the_title( $before, $after, $echo );
	}
}

/**
 * Generate a nicely formatted post title
 *
 * Ignore empty titles, titles that are auto-generated from the
 * first part of the post_content
 *
 * @package WordPress
 * @subpackage Chatter
 * @since 1.0.5
 *
 * @param    string    $before    content to prepend to title
 * @param    string    $after     content to append to title
 * @param    string    $echo      echo or return
 * @return   string    $out       nicely formatted title, will be boolean(false) if no title
 */
function Chatter_the_title( $before = '<h2>', $after = '</h2>', $echo = true ) {
	global $post;

	$temp = $post;
	$t = apply_filters( 'the_title', $temp->post_title, $temp->ID );
	$title = $temp->post_title;
	$content = $temp->post_content;
	$pos = 0;
	$out = '';

	// Don't show post title if turned off in options or title is default text
	if ( 1 != (int) get_option( 'prologue_show_titles' ) || 'Post Title' == $title )
		return false;

	$content = trim( $content );
	$title = trim( $title );
	$title = preg_replace( '/\.\.\.$/', '', $title );
	$title = str_replace( "\n", ' ', $title );
	$title = str_replace( '  ', ' ', $title);
	$content = str_replace( "\n", ' ', strip_tags( $content) );
	$content = str_replace( '  ', ' ', $content );
	$content = trim( $content );
	$title = trim( $title );

	// Clean up links in the title
	if ( false !== strpos( $title, 'http' ) )  {
		$split = @str_split( $content, strpos( $content, 'http' ) );
		$content = $split[0];
		$split2 = @str_split( $title, strpos( $title, 'http' ) );
		$title = $split2[0];
	}

	// Avoid processing an empty title
	if ( '' == $title )
		return false;

	// Avoid processing the title if it's the very first part of the post content,
	// which is the case with most "status" posts
	$pos = strpos( $content, $title );
	if ( '' == get_post_format() || false === $pos || 0 < $pos ) {
		if ( is_single() )
			$out = $before . $t . $after;
		else
			$out = $before . '<a href="' . get_permalink( $temp->ID ) . '">' . $t . '&nbsp;</a>' . $after;

		if ( $echo )
			echo $out;
		else
			return $out;
	}

	return false;
}

function Chatter_comments( $comment, $args ) {
	$GLOBALS['comment'] = $comment;

	if ( !is_single() && get_comment_type() != 'comment' )
		return;

	$depth          = prologue_get_comment_depth( get_comment_ID() );
	$can_edit_post  = current_user_can( 'edit_post', $comment->comment_post_ID );

	$reply_link     = prologue_get_comment_reply_link(
		array( 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => ' | ', 'reply_text' => __( 'Reply', 'Chatter' ) ),
		$comment->comment_ID, $comment->comment_post_ID );

	$content_class  = 'commentcontent';
	if ( $can_edit_post )
		$content_class .= ' comment-edit';

	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<?php do_action( 'Chatter_comment' ); ?>

		<?php echo get_avatar( $comment, 32 ); ?>
		
			<p style="padding-left:3em;"><a href="author/<?php echo comment_author(); ?>"><?php echo comment_author(); ?></a>
			
				<small><a href="
			<?php echo  the_permalink() .'"" class="thepermalink">'. Chatter_chatter_time_ago('comment') .'</a></small>';
			 ?></p><span class="meta">
			
				<span class="actions">
					<a class="thepermalink" href="<?php echo esc_url( get_comment_link() ); ?>" title="<?php esc_attr_e( 'Permalink', 'Chatter' ); ?>"><?php _e( 'Permalink', 'Chatter' ); ?></a>
					<?php
					echo $reply_link;

					if ( $can_edit_post )
						edit_comment_link( __( 'Edit', 'Chatter' ), ' | ' );

					?>
				</span>
			</span>
		<div id="commentcontent-<?php comment_ID(); ?>" class="<?php echo esc_attr( $content_class ); ?>"><?php
				echo apply_filters( 'comment_text', $comment->comment_content, $comment );

				if ( $comment->comment_approved == '0' ): ?>
					<p><em><?php esc_html_e( 'Your comment is awaiting moderation.', 'Chatter' ); ?></em></p>
				<?php endif; ?>
		</div>
	<?php
}

function get_tags_with_count( $post, $format = 'list', $before = '', $sep = '', $after = '' ) {
	$posttags = get_the_tags($post->ID, 'post_tag' );

	if ( !$posttags )
		return '';

	foreach ( $posttags as $tag ) {
		if ( $tag->count > 1 && !is_tag($tag->slug) ) {
			$tag_link = '<a href="' . get_tag_link( $tag ) . '" rel="tag">' . $tag->name . ' ( ' . number_format_i18n( $tag->count ) . ' )</a>';
		} else {
			$tag_link = $tag->name;
		}

		if ( $format == 'list' )
			$tag_link = '<li>' . $tag_link . '</li>';

		$tag_links[] = $tag_link;
	}

	return apply_filters( 'tags_with_count', $before . join( $sep, $tag_links ) . $after, $post );
}

function tags_with_count( $format = 'list', $before = '', $sep = '', $after = '' ) {
	global $post;
	echo get_tags_with_count( $post, $format, $before, $sep, $after );
}

function Chatter_title_from_content( $content ) {
	$title = Chatter_excerpted_title( $content, 8 ); // limit title to 8 full words

	// Try to detect image or video only posts, and set post title accordingly
	if ( empty( $title ) ) {
		if ( preg_match("/<object|<embed/", $content ) )
			$title = __( 'Video Post', 'Chatter' );
		elseif ( preg_match( "/<img/", $content ) )
			$title = __( 'Image Post', 'Chatter' );
	}

	return $title;
}

function Chatter_excerpted_title( $content, $word_count ) {
	$content = strip_tags( $content );
	$words = preg_split( '/([\s_;?!\/\(\)\[\]{}<>\r\n\t"]|\.$|(?<=\D)[:,.\-]|[:,.\-](?=\D))/', $content, $word_count + 1, PREG_SPLIT_NO_EMPTY );

	if ( count( $words ) > $word_count ) {
		array_pop( $words ); // remove remainder of words
		$content = implode( ' ', $words );
		$content = $content . '...';
	} else {
		$content = implode( ' ', $words );
	}

	$content = trim( strip_tags( $content ) );

	return $content;
}

function Chatter_add_reply_title_attribute( $link ) {
	return str_replace( "rel='nofollow'", "rel='nofollow' title='" . __( 'Reply', 'Chatter' ) . "'", $link );
}
add_filter( 'post_comments_link', 'Chatter_add_reply_title_attribute' );

function Chatter_fix_empty_titles( $data, $postarr ) {
	if ( 'post' != $data['post_type'] )
		return $data;

	if ( ! empty( $postarr['post_title'] ) )
		return $data;

	$data['post_title'] = Chatter_title_from_content( $data['post_content'] );

	return $data;
}
add_filter( 'wp_insert_post_data', 'Chatter_fix_empty_titles', 10, 2 );

function Chatter_add_head_content() {
	if ( is_home() && is_user_logged_in() ) {
		include_once( ABSPATH . '/wp-admin/includes/media.php' );
	}
}
add_action( 'wp_head', 'Chatter_add_head_content' );

function Chatter_new_post_noajax() {
	if ( empty( $_POST['action'] ) || $_POST['action'] != 'post' )
	    return;

	if ( !is_user_logged_in() )
		auth_redirect();

	if ( !current_user_can( 'publish_posts' ) ) {
		wp_redirect( home_url( '/' ) );
		exit;
	}

	$current_user = wp_get_current_user();

	check_admin_referer( 'new-post' );

	$user_id        = $current_user->ID;
	$post_content   = $_POST['posttext'];
	$tags           = $_POST['tags'];

	$post_title = Chatter_title_from_content( $post_content );

	$post_id = wp_insert_post( array(
		'post_author'   => $user_id,
		'post_title'    => $post_title,
		'post_content'  => $post_content,
		'tags_input'    => $tags,
		'post_status'   => 'publish'
	) );

	$post_format = 'status';
	if ( in_array( $_POST['post_format'], Chatter_get_supported_post_formats() ) )
		$post_format = $_POST['post_format'];

	set_post_format( $post_id, $post_format );

	wp_redirect( home_url( '/' ) );

	exit;
}
add_filter( 'template_redirect', 'Chatter_new_post_noajax' );

/**
 * iPhone Stylesheet.
 *
 * Hooks into the wp_enqueue_scripts action late.
 *
 * @uses Chatter_is_iphone()
 * @since Chatter 1.4
 */

/**
 * Print Stylesheet.
 *
 * Hooks into the wp_enqueue_scripts action.
 *
 * @since Chatter 1.5
 */
function Chatter_print_style() {
	wp_enqueue_style( 'Chatter', get_stylesheet_uri() );
	wp_enqueue_style( 'Chatter-print-style', get_template_directory_uri() . '/style-print.css', array( 'Chatter' ), '20120807', 'print' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );
}
add_action( 'wp_enqueue_scripts', 'Chatter_print_style' );

/*
	Modified to replace query string with blog url in output string
*/
function prologue_get_comment_reply_link( $args = array(), $comment = null, $post = null ) {
	global $user_ID;

	if ( post_password_required() )
		return;

	$defaults = array( 'add_below' => 'commentcontent', 'respond_id' => 'respond', 'reply_text' => __( 'Reply', 'Chatter' ),
		'login_text' => __( 'Log in to Reply', 'Chatter' ), 'depth' => 0, 'before' => '', 'after' => '' );

	$args = wp_parse_args($args, $defaults);
	if ( 0 == $args['depth'] || $args['max_depth'] <= $args['depth'] )
		return;

	extract($args, EXTR_SKIP);

	$comment = get_comment($comment);
	$post = get_post($post);

	if ( 'open' != $post->comment_status )
		return false;

	$link = '';

	$reply_text = esc_html( $reply_text );

	if ( get_option( 'comment_registration' ) && !$user_ID )
		$link = '<a rel="nofollow" href="' . site_url( 'wp-login.php?redirect_to=' . urlencode( get_permalink() ) ) . '">' . esc_html( $login_text ) . '</a>';
	else
		$link = "<a rel='nofollow' class='comment-reply-link' href='". get_permalink($post). "#" . urlencode( $respond_id ) . "' title='". __( 'Reply', 'Chatter' )."' onclick='return addComment.moveForm(\"" . esc_js( "$add_below-$comment->comment_ID" ) . "\", \"$comment->comment_ID\", \"" . esc_js( $respond_id ) . "\", \"$post->ID\")'>$reply_text</a>";
	return apply_filters( 'comment_reply_link', $before . $link . $after, $args, $comment, $post);
}

function prologue_comment_depth_loop( $comment_id, $depth )  {
	$comment = get_comment( $comment_id );

	if ( isset( $comment->comment_parent ) && 0 != $comment->comment_parent ) {
		return prologue_comment_depth_loop( $comment->comment_parent, $depth + 1 );
	}
	return $depth;
}

function prologue_get_comment_depth( $comment_id ) {
	return prologue_comment_depth_loop( $comment_id, 1 );
}

function prologue_comment_depth( $comment_id ) {
	echo prologue_get_comment_depth( $comment_id );
}

function prologue_poweredby_link() {
	return apply_filters( 'prologue_poweredby_link', sprintf( '<a href="%1$s" rel="generator">%2$s</a>', esc_url( __('http://wordpress.org/', 'Chatter') ), sprintf( __('Proudly powered by %s.', 'Chatter'), 'WordPress' ) ) );
}



// Network signup form
function Chatter_before_signup_form() {
	echo '<div class="sleeve_main"><div id="main">';
}
add_action( 'before_signup_form', 'Chatter_before_signup_form' );

function Chatter_after_signup_form() {
	echo '</div></div>';
}
add_action( 'after_signup_form', 'Chatter_after_signup_form' );

/**
 * Returns accepted post formats.
 *
 * The value should be a valid post format registered for Chatter, or one of the back compat categories.
 * post formats: link, quote, standard, status
 * categories: link, post, quote, status
 *
 * @since Chatter 1.3.4
 *
 * @param string type Which data to return (all|category|post-format)
 * @return array
 */
function Chatter_get_supported_post_formats( $type = 'all' ) {
	$post_formats = array( 'link', 'quote', 'status' );

	switch ( $type ) {
		case 'post-format':
			break;
		case 'category':
			$post_formats[] = 'post';
			break;
		case 'all':
		default:
			array_push( $post_formats, 'post', 'standard' );
			break;
	}

	return apply_filters( 'Chatter_get_supported_post_formats', $post_formats );
}

/**
 * Is site being viewed on an iPhone or iPod Touch?
 *
 * For testing you can modify the output with a filter:
 * add_filter( 'Chatter_is_iphone', '__return_true' );
 *
 * @return bool
 * @since Chatter 1.4
 */
function Chatter_is_iphone() {
	$output = false;

	if ( ( isset( $_SERVER['HTTP_USER_AGENT'] ) && strstr( $_SERVER['HTTP_USER_AGENT'], 'iPhone' ) && ! strstr( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) ) || isset( $_GET['iphone'] ) && $_GET['iphone'] )
		$output = true;

	$output = (bool) apply_filters( 'Chatter_is_iphone', $output );

	return $output;
}

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since Chatter 1.5
 */
function Chatter_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'Chatter' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'Chatter_wp_title', 10, 2 );


function allow_pending_posts($qry) {
  if (!is_admin() && current_user_can('edit_posts')) {
    $qry->set('post_status', array('publish','pending'));
  }
}
add_action('pre_get_posts','allow_pending_posts');

/**
 * Chatter-likes
 * http://wordpress.org/plugins/Chatter-likes/
 */
if ( defined( 'ChatterLIKES_URL' ) ) {
	require_once( get_template_directory() . '/inc/integrations/Chatter-likes/Chatter-likes.php' );
}



/**
 * Time Ago
 * Changes the time format to a twitterlike time ago.
 */


function Chatter_chatter_time_ago() {
 
	global $post;
 
	$date = get_post_time('G', true, $post);
 
	// Array of time period chunks
	$chunks = array(
		array( 60 * 60 * 24 * 365 , __( 'year', 'Chatter_chatter' ), __( 'years', 'Chatter_chatter' ) ),
		array( 60 * 60 * 24 * 30 , __( 'month', 'Chatter_chatter' ), __( 'months', 'Chatter_chatter' ) ),
		array( 60 * 60 * 24 * 7, __( 'week', 'Chatter_chatter' ), __( 'weeks', 'Chatter_chatter' ) ),
		array( 60 * 60 * 24 , __( 'day', 'Chatter_chatter' ), __( 'days', 'Chatter_chatter' ) ),
		array( 60 * 60 , __( 'hour', 'Chatter_chatter' ), __( 'hours', 'Chatter_chatter' ) ),
		array( 60 , __( 'minute', 'Chatter_chatter' ), __( 'minutes', 'Chatter_chatter' ) ),
		array( 1, __( 'second', 'Chatter_chatter' ), __( 'seconds', 'Chatter_chatter' ) )
	);
 
	if ( !is_numeric( $date ) ) {
		$time_chunks = explode( ':', str_replace( ' ', ':', $date ) );
		$date_chunks = explode( '-', str_replace( ' ', '-', $date ) );
		$date = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
	}
 
	$current_time = current_time( 'mysql', $gmt = 0 );
	$newer_date = strtotime( $current_time );
 
	// Difference in seconds
	$since = $newer_date - $date;
 
	// Something went wrong with date calculation and we ended up with a negative date.
	if ( 0 > $since )
		return __( 'sometime', 'Chatter_chatter' );
 
	/**
	 * We only want to output one chunks of time here, eg:
	 * x years
	 * xx months
	 * so there's only one bit of calculation below:
	 */
 
	//Step one: the first chunk
	for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
		$seconds = $chunks[$i][0];
 
		// Finding the biggest chunk (if the chunk fits, break)
		if ( ( $count = floor($since / $seconds) ) != 0 )
			break;
	}
 
	// Set output var
	$output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];
 
 
	if ( !(int)trim($output) ){
		$output = '0 ' . __( 'seconds', 'Chatter_chatter' );
	}
 
	$output .= __(' ago', 'Chatter_chatter');
 
	return $output;
}
 
// Filter our Chatter_chatter_time_ago() function into WP's the_time() function
add_filter('the_time', 'Chatter_chatter_time_ago');

function Chatter_chatter_SearchFilter($query) {
if ($query->is_search) {
$query->set('post_type', 'post');
}
return $query;
}
add_filter('pre_get_posts','Chatter_chatter_SearchFilter');
