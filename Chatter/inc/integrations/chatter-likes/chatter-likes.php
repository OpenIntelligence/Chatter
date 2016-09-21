<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Integrates Chatter with the Chatter-likes plugin
 * http://wordpress.org/plugins/Chatter-likes/
 */

add_action( 'init', 'Chatter_likes_custom' );
function Chatter_likes_custom() {
	// Removes the 'like' button from posts and comments
	remove_action( 'Chatter_action_links', 'Chatter_likes_action_links' );
	remove_filter( 'comment_reply_link', 'Chatter_likes_comment_reply_link', 99, 4 );

	// Adds our own 'like' button with some simple markup tweaks
	add_action( 'Chatter_action_links', 'Chatter_likes_action_links' );
	add_filter( 'comment_reply_link', 'Chatter_likes_comment_reply_link', 5, 4 );
}

function Chatter_likes_action_links() {
	global $post;
	global $current_user;
	$postmeta = get_post_meta( $post->ID, '_Chatter_likes', true );
	$users = Chatter_likes_generate_users_html($postmeta);
	$like_count = ( $postmeta ? count($postmeta) : 0 );
	$like_text = ( $postmeta && in_array( $current_user->ID, $postmeta ) ? __( 'Unlike', 'Chatter-likes' ) : __( 'Like', 'Chatter-likes' ) );
	echo " | <div class='Chatter-likes-link'><a rel='nofollow' class='Chatter-likes-post Chatter-likes-post-".$post->ID."' href='". get_permalink($post). "' title='".$like_text."' onclick='ChatterLikes(0,".$post->ID."); return false;'><span class='Chatter-likes-like'>".$like_text."</span><span class='Chatter-likes-count'>".$like_count."</span></a><div class='Chatter-likes-box'>".$users."</div></div>";
}

function Chatter_likes_comment_reply_link( $link, $args, $comment, $post ) {
	global $post;
	global $comment;
	global $current_user;
	$commentmeta = get_comment_meta( $comment->comment_ID, '_Chatter_likes', true );
	$users = Chatter_likes_generate_users_html($commentmeta);
	$like_count = ( $commentmeta ? count($commentmeta) : 0 );
	$like_text = ( $commentmeta && in_array( $current_user->ID, $commentmeta ) ? __( 'Unlike', 'Chatter-likes' ) : __( 'Like', 'Chatter-likes' ) );
	$output = " | <div class='Chatter-likes-link'><a rel='nofollow' class='Chatter-likes-link Chatter-likes-comment Chatter-likes-comment-".$comment->comment_ID."' href='". get_permalink($post). "' title='".$like_text."' onclick='ChatterLikes(1,".$comment->comment_ID."); return false;'><span class='Chatter-likes-like'>".$like_text."</span><span class='Chatter-likes-count'>".$like_count."</span></a><div class='Chatter-likes-box'>".$users."</div></div>";
	return $link . $output;
}