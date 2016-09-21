<?php
/**
 * Setup and callbacks for WordPress custom header feature.
 *
 * @package Chatter
 * @since Chatter 1.4
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of get_custom_header() which was introduced
 * in WordPress 3.4.
 *
 * @uses Chatter_header_style()
 * @uses Chatter_admin_header_style()
 *
 * @package Chatter
 * @since Chatter 1.4
 */
function Chatter_setup_custom_header() {
	add_theme_support( 'custom-header', apply_filters( 'Chatter_custom_header_args', array(
		'width'               => 1680,
		'height'              => 420,
		'default-image'       => 'f',
		'default-text-color'  => '3478e3',
		'wp-head-callback'    => 'Chatter_header_style',
		'admin-head-callback' => 'Chatter_admin_header_style',
	) ) );
}

/**
 * Styles for the Custom Header admin UI.
 *
 * @package Chatter
 * @since Chatter 1.1
 */
function Chatter_admin_header_style() {
?>
	<style type="text/css">
	#headimg {
		background: url('<?php echo esc_url( get_header_image() ); ?>') repeat;
		padding: 0 0 0 10px;
		width: <?php echo get_custom_header()->width; ?>px;
		height: <?php echo get_custom_header()->height; ?>px;
	}
	#headimg a {
		width: <?php echo get_custom_header()->width; ?>px;
		height: <?php echo get_custom_header()->height; ?>px;
	}
	#headimg h1 {
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-weight: 200;
		margin: 0;
		padding-top: 20px;
	}
	#headimg h1 a {
		color: #<?php header_textcolor(); ?>;
		border-bottom: none;
		font-size: 40px;
		margin: -0.4em 0 0 0;
		text-decoration: none;
	}
	#headimg #desc {
		color: #<?php header_textcolor(); ?>;
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, sans-serif;
		font-size: 13px;
		font-weight: 400;
		margin-top: 1em;
	}

	<?php if ( 'blank' == get_header_textcolor() ) : ?>
	#headimg h1,
	#headimg #desc {
		display: none;
	}
	#headimg h1 a,
	#headimg #desc {
		color: #<?php echo get_header_textcolor(); ?>;
	}
	<?php endif; ?>

	</style>
<?php
}

/**
 * Styles to display custom header in template files.
 *
 * @package Chatter
 * @since Chatter 1.1
 */
function Chatter_header_style() {
?>
	<style id="Chatter-header-style" type="text/css">
	<?php if ( '' != get_header_image() ) : ?>
	<?php echo get_random_header_image() ?>
        #header-image {
            background: url('<?php echo esc_url( get_header_image() ); ?>') repeat;
            height:400px;
            padding-bottom:1.6em;
            border-bottom: 1px solid #dddddd;
            -webkit-box-shadow: 0 0.327em 0 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 2.618em;
            position: relative;

            }
            
                 .sleeve_main {
            margin-top:-190px;
            z-index: 4 !important;
            position:relative;
            }
		
            
            @media only screen and (min-width: 120px) and (max-width: 768px) {
.lwa-info,
 #header-image,
 #sidebar,
    #side-avatar {
    display: none;
    }

         .sleeve_main {
            margin-top:5px;
            z-index: 4 !important;
            position:relative;
            }
		
    
}

		
   
	<?php endif;?>
	</style>
<?php
}
