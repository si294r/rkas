<?php
defined( 'ABSPATH' ) || die(); // security access WordPress context only.

// custom function to unslash and sanitize before accessing globals $_POST and $_GET.
if ( ! function_exists( 'wp_request' ) ) {
	function wp_request( $name ) {
		return isset( $_REQUEST[ $name ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $name ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}

// custom function to unslash and sanitize before accessing globals $_POST and $_GET.
// this function preserves new line and other whitespace.
if ( ! function_exists( 'wp_request_textarea' ) ) {
	function wp_request_textarea( $name ) {
		return isset( $_REQUEST[ $name ] ) ? sanitize_textarea_field( wp_unslash( $_REQUEST[ $name ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}
