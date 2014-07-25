<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * Get rtBiz Templates
 *
 * @param $template_name
 * @param array $args
 * @param string $template_path
 * @param string $default_path
 */
function rt_biz_export_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	if ( $args && is_array($args) )
		extract( $args );

	$located = rt_biz_export_locate_template( $template_name, $template_path, $default_path );

	do_action( 'rt_biz_export_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'rt_biz_export_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Loads rtBiz Templates
 *
 * @param $template_name
 * @param string $template_path
 * @param string $default_path
 * @return mixed|void
 */
function rt_biz_export_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	global $rt_biz_export;
	if ( ! $template_path ) $template_path = $rt_biz_export->templateURL;
	if ( ! $default_path ) $default_path = RT_BIZ_EXPORT_PATH_TEMPLATES;

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template )
		$template = $default_path . $template_name;

	// Return what we found
	return apply_filters('rt_biz_export_locate_template', $template, $template_name, $template_path);
}
