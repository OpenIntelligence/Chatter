<?php
/**
 * Utility functions.
 *
 * @package Chatter
 * @since Chatter 0.1
 */

function Chatter_maybe_define( $constant, $value, $filter = '' ) {
	if ( defined( $constant ) )
		return;

	if ( !empty( $filter ) )
		$value = apply_filters( $filter, $value );

	define( $constant, $value );
}
