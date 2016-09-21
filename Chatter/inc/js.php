<?php
/**
 * Script handler.
 *
 * @package Chatter
 * @since Chatter 0.1
 */
class Chatter_JS {

	static function init() {
		add_action( 'wp_enqueue_scripts', array( 'Chatter_JS', 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( 'Chatter_JS', 'enqueue_styles' ) );
		add_action( 'wp_head', array( 'Chatter_JS', 'print_options' ), 1 );

		/**
		 * Register scripts
		 */
		wp_register_script(
			'jeditable',
			Chatter_JS_URL . '/jquery.jeditable.js',
			array( 'jquery' ),
			'1.6.2-rc2' );

		wp_register_script(
			'caret',
			Chatter_JS_URL . '/caret.js',
			array('jquery'),
			'20101025' );

		wp_register_script(
			'jquery-ui-autocomplete-html',
			Chatter_JS_URL . '/jquery.ui.autocomplete.html.js',
			array( 'jquery-ui-autocomplete' ),
			'20101025' );

		wp_register_script(
			'jquery-ui-autocomplete-multiValue',
			Chatter_JS_URL . '/jquery.ui.autocomplete.multiValue.js',
			array( 'jquery-ui-autocomplete' ),
			'20110405' );

		wp_register_script(
			'jquery-ui-autocomplete-match',
			Chatter_JS_URL . '/jquery.ui.autocomplete.match.js',
			array( 'jquery-ui-autocomplete', 'caret' ),
			'20110405' );

		/**
		 * Bundle containing scripts included when the user is logged in.
		 * Includes, in order:
		 *     jeditable, caret, jquery-ui-autocomplete,
		 *     jquery-ui-autocomplete-html, jquery-ui-autocomplete-multiValue,
		 *     jquery-ui-autocomplete-match
		 *
		 * Build the bundle with the bin/bundle-user-js shell script.
		 *
		 * @TODO: Improve bundle building/dependency process.
		 */
		wp_register_script(
			'Chatter-user-bundle',
			Chatter_JS_URL . '/Chatter.user.bundle.js',
			array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ),
			'20130819' );

		wp_register_script(
			'scrollit',
			Chatter_JS_URL .'/jquery.scrollTo-min.js',
			array( 'jquery' ),
			'20120402' );

		wp_register_script(
			'wp-locale',
			Chatter_JS_URL . '/wp-locale.js',
			array(),
			'20130819' );

		// Media upload script registered based on info in script-loader.
		wp_register_script(
			'media-upload',
			'/wp-admin/js/media-upload.js',
			array( 'thickbox' ),
			'20110113' );

		wp_register_script(
			'Chatter-spin',
			Chatter_JS_URL .'/spin.js',
			array( 'jquery' ),
			'20120704'
		);
	}

	static function enqueue_styles() {
		if ( is_home() && is_user_logged_in() )
			wp_enqueue_style( 'thickbox' );

		if ( is_user_logged_in() ) {
			wp_enqueue_style( 'jquery-ui-autocomplete', Chatter_JS_URL . '/jquery.ui.autocomplete.css', array(), '1.8.11' );
		}
	}

	static function enqueue_scripts() {
		global $wp_locale;

		// Generate dependencies for Chatter
		$depends = array( 'jquery', 'utils', 'jquery-color', 'comment-reply',
			'scrollit', 'wp-locale', 'Chatter-spin' );

		if ( is_user_logged_in() ) {
			$depends[] = 'jeditable';
			$depends[] = 'jquery-ui-autocomplete-html';
			$depends[] = 'jquery-ui-autocomplete-multiValue';
			$depends[] = 'jquery-ui-autocomplete-match';

			// media upload
			if ( is_home() ) {
				$depends[] = 'media-upload';
			}
		}

		// Enqueue Chatter JS
		wp_enqueue_script( 'Chatterjs',
			Chatter_JS_URL . '/Chatter.js',
			$depends,
			'20140603'
		);

		wp_localize_script( 'Chatterjs', 'Chattertxt', array(
			'tags'                  => '<br />' . __( 'Tags:' , 'Chatter' ),
			'tagit'                 => __( 'Tag it', 'Chatter' ),
			'citation'              => __( 'Citation', 'Chatter' ),
			'title'                 => __( 'Post Title', 'Chatter' ),
			'goto_homepage'         => __( 'Go to homepage', 'Chatter' ),
			// the number is calculated in the javascript in a complex way, so we can't use ngettext
			'n_new_updates'         => __( '%d new update(s)', 'Chatter' ),
			'n_new_comments'        => __( '%d new comment(s)', 'Chatter' ),
			'jump_to_top'           => __( 'Jump to top', 'Chatter' ),
			'not_posted_error'      => __( 'An error has occurred, your post was not posted', 'Chatter' ),
			'update_posted'         => __( 'Your update has been posted', 'Chatter' ),
			'loading'               => __( 'Loading...', 'Chatter' ),
			'cancel'                => __( 'Cancel', 'Chatter' ),
			'save'                  => __( 'Save', 'Chatter' ),
			'hide_threads'          => __( 'Hide threads', 'Chatter' ),
			'show_threads'          => __( 'Show threads', 'Chatter' ),
			'unsaved_changes'       => __( 'Your comments or posts will be lost if you continue.', 'Chatter' ),
			'date_time_format'      => __( '%1$s <em>on</em> %2$s', 'Chatter' ),
			'date_format'           => get_option( 'date_format' ),
			'time_format'           => get_option( 'time_format' ),
			// if we don't convert the entities to characters, we can't get < and > inside
			'l10n_print_after'      => 'try{convertEntities(Chattertxt);}catch(e){};',
			'autocomplete_prompt'   => __( 'After typing @, type a name or username to find a member of this site', 'Chatter' ),
			'no_matches'            => __( 'No matches.', 'Chatter' ),
			'comment_cancel_ays'    => __( 'Are you sure you would like to clear this comment? Its contents will be deleted.', 'Chatter' ),
			'oops_not_logged_in'    => __( 'Oops! Looks like you are not logged in.', 'Chatter' ),
			'please_log_in'         => __( 'Please log in again', 'Chatter' ),
			'whoops_maybe_offline'  => __( 'Whoops! Looks like you are not connected to the server. Chatter could not connect with WordPress.', 'Chatter' ),
			'required_filed'        => __( 'This field is required.', 'Chatter' ),
		) );

		if ( Chatter_is_iphone() ) {
			wp_enqueue_script(
				'iphone',
				get_template_directory_uri() . '/js/iphone.js',
				array( 'jquery' ),
				'20120402',
				true
			);
		}

		add_action( 'wp_head', array( 'Chatter_JS', 'locale_script_data' ), 2 );
	}

	static function locale_script_data() {
		global $wp_locale;
		?>
		<script type="text/javascript">
		//<![CDATA[
		var wpLocale = <?php echo get_js_locale( $wp_locale ); ?>;
		//]]>
		</script>
		<?php
	}

	static function ajax_url() {
		global $current_blog;

		// Generate the ajax url based on the current scheme
		$admin_url = admin_url( 'admin-ajax.php?Chatterajax=true', is_ssl() ? 'https' : 'http' );
		// If present, take domain mapping into account
		if ( isset( $current_blog->primary_redirect ) )
			$admin_url = preg_replace( '|https?://' . preg_quote( $current_blog->domain ) . '|', 'http://' . $current_blog->primary_redirect, $admin_url );
		return $admin_url;
	}

	static function ajax_read_url() {
		return add_query_arg( 'Chatterajax', 'true', get_feed_link( 'Chatter.ajax' ) );
	}

	static function print_options() {
		$mentions = Chatter_get( 'mentions' );

		get_currentuserinfo();
		$page_options['nonce']= wp_create_nonce( 'ajaxnonce' );
		$page_options['prologue_updates'] = 1;
		$page_options['prologue_comments_updates'] = 1;
		$page_options['prologue_tagsuggest'] = 1;
		$page_options['prologue_inlineedit'] = 1;
		$page_options['prologue_comments_inlineedit'] = 1;
		$page_options['is_single'] = (int)is_single();
		$page_options['is_page'] = (int)is_page();
		$page_options['is_front_page'] = (int)is_front_page();
		$page_options['is_first_front_page'] = (int)(is_front_page() && !is_paged() );
		$page_options['is_user_logged_in'] = (int)is_user_logged_in();
		$page_options['login_url'] = wp_login_url( ( ( !empty($_SERVER['HTTPS'] ) && strtolower($_SERVER['HTTPS']) == 'on' ) ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
?>
		<script type="text/javascript">
			// <![CDATA[

			// Chatter Configuration
			var ajaxUrl                 = "<?php echo esc_js( esc_url_raw( Chatter_JS::ajax_url() ) ); ?>";
			var ajaxReadUrl             = "<?php echo esc_js( esc_url_raw( Chatter_JS::ajax_read_url() ) ); ?>";
			var updateRate              = "30000"; // 30 seconds
			var nonce                   = "<?php echo esc_js( $page_options['nonce'] ); ?>";
			var login_url               = "<?php echo $page_options['login_url'] ?>";
			var templateDir             = "<?php echo esc_js( get_template_directory_uri() ); ?>";
			var isFirstFrontPage        = <?php echo $page_options['is_first_front_page'] ?>;
			var isFrontPage             = <?php echo $page_options['is_front_page'] ?>;
			var isSingle                = <?php echo $page_options['is_single'] ?>;
			var isPage                  = <?php echo $page_options['is_page'] ?>;
			var isUserLoggedIn          = <?php echo $page_options['is_user_logged_in'] ?>;
			var prologueTagsuggest      = <?php echo $page_options['prologue_tagsuggest'] ?>;
			var prologuePostsUpdates    = <?php echo $page_options['prologue_updates'] ?>;
			var prologueCommentsUpdates = <?php echo $page_options['prologue_comments_updates']; ?>;
			var getPostsUpdate          = 0;
			var getCommentsUpdate       = 0;
			var inlineEditPosts         = <?php echo $page_options['prologue_inlineedit'] ?>;
			var inlineEditComments      = <?php echo $page_options['prologue_comments_inlineedit'] ?>;
			var wpUrl                   = "<?php echo esc_js( site_url() ); ?>";
			var rssUrl                  = "<?php esc_js( get_bloginfo( 'rss_url' ) ); ?>";
			var pageLoadTime            = "<?php echo gmdate( 'Y-m-d H:i:s' ); ?>";
			var commentsOnPost          = new Array;
			var postsOnPage             = new Array;
			var postsOnPageQS           = '';
			var currPost                = -1;
			var currComment             = -1;
			var commentLoop             = false;
			var lcwidget                = false;
			var hidecomments            = false;
			var commentsLists           = '';
			var newUnseenUpdates        = 0;
			var mentionData             = <?php echo json_encode( $mentions->user_suggestion() ); ?>;
			var ChatterCurrentVersion        = <?php echo (int) $GLOBALS['Chatter']->db_version; ?>;
			var ChatterStoredVersion         = <?php echo (int) $GLOBALS['Chatter']->get_option( 'db_version' ); ?>;
			// ]]>
		</script>
<?php }
}
add_action( 'init', array( 'Chatter_JS', 'init' ) );

function Chatter_toggle_threads() {
	$hide_threads = get_option( 'Chatter_hide_threads' ); ?>

	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery( document ).ready( function( $ ) {
			function hideComments() {
				$('.commentlist').hide();
				$('.discussion').show();
			}
			function showComments() {
				$('.commentlist').show();
				$('.discussion').hide();
			}
			<?php if ( (int) $hide_threads && ! is_singular() ) : ?>
				hideComments();
			<?php endif; ?>

			$( "#togglecomments" ).click( function() {
				if ( $( '.commentlist' ).css( 'display' ) == 'none' ) {
					showComments();
				} else {
					hideComments();
				}
				return false;
			});
		});
	/* ]]> */
	</script><?php
}
add_action( 'wp_footer', 'Chatter_toggle_threads' );
