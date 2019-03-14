<?php
/**
Plugin Name: Inline Spoilers
Plugin URI: https://wordpress.org/plugins/inline-spoilers/
Description: The plugin allows to create content spoilers with simple shortcode.
Version: 1.3.8
Author: Sergey Kuzmich
Author URI: http://kuzmi.ch
Text Domain: inline-spoilers
Domain Path: /languages/
License: GPLv3
*/

/**
 * @package Inline Spoilers
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'plugins_loaded', 'is_load_textdomain' );
function is_load_textdomain() {
	load_plugin_textdomain( 'inline-spoilers', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_shortcode( 'spoiler', 'is_spoiler_shortcode' );
function is_spoiler_shortcode( $atts, $content ) {
	$attributes = shortcode_atts(
		array(
			'title'         => '&nbsp;',
			'initial_state' => 'collapsed',
		),
		$atts,
		'spoiler'
	);

	$initial_state = esc_attr( $attributes['initial_state'] );
	$title         = esc_attr( $attributes['title'] );

	$props = ( $initial_state === 'collapsed' ) ? [
								'head_class' => ' collapsed',
								'body_atts'  => 'style="display: none;"',
								'head_hint'  => __( 'Expand', 'inline-spoilers' )
							] : [
								'head_class' => ' expanded',
								'body_atts'  => 'style="display: block;"',
								'head_hint'  => __( 'Collapse', 'inline-spoilers' )
							];

	$head  = '<div class="spoiler-head no-icon ' . $props['head_class'] . '" title="' . $props['head_hint'] . '">' . $title . '</div>';

	$body  = '<div class="spoiler-body" ' . $props['body_atts'] . '>';
	$body .= balanceTags( do_shortcode( $content ), true );
	$body .= '</div>';

	$extra  = '<div class="spoiler-body">';
	$extra .= balanceTags( do_shortcode( $content ), true );
	$extra .= '</div>';

	$output  = '<div class="spoiler-wrap">';
	$output .= $head . $body;
	$output .= ( $initial_state === 'collapsed' )
								? '<noscript>' . $extra . '</noscript>' : '';
	$output .= '</div>';

	return $output;
}

add_action( 'wp_enqueue_scripts', 'is_styles_scripts' );
function is_styles_scripts() {
	wp_register_style( 'inline-spoilers_style', plugins_url( 'styles/inline-spoilers-default.css', __FILE__ ), null, '1.0' );
	wp_register_script( 'inline-spoilers_script', plugins_url( 'scripts/inline-spoilers-scripts.js', __FILE__ ), array( 'jquery' ), '1.0', true );

	wp_enqueue_style( 'inline-spoilers_style' );
	wp_enqueue_script( 'inline-spoilers_script' );

	$translation_array = array(
		'expand'   => __( 'Expand', 'inline-spoilers' ),
		'collapse' => __( 'Collapse', 'inline-spoilers' ),
	);

	wp_localize_script( 'inline-spoilers_script', 'title', $translation_array );
}

add_action( 'init', 'spoiler_block_init' );
function spoiler_block_init() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	wp_register_script( 'block-editor', plugins_url( 'block/index.js', __FILE__ ), array( 'wp-blocks', 'wp-i18n', 'wp-element', ), '1.0', true );
	wp_register_style( 'block-editor', plugins_url( 'block/editor.css', __FILE__ ), array(), '1.0' );
	wp_register_style( 'block', plugins_url( 'block/style.css', __FILE__ ), array(), '1.0' );

	register_block_type(
		'inline-spoilers/block',
		array(
			'editor_script' => 'block-editor',
			'editor_style'  => 'block-editor',
			'style'         => 'block',
		)
	);
}
