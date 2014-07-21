<?php

/*
  Plugin Name: rtBiz Data Exporter
  Plugin URI: http://rtcamp.com
  Description: Data Exporter in rtBiz for Contacts, Accounts etc. in different formats such as CSV, JSON, XML etc.
  Version: 0.0.1
  Author: rtCamp
  Author URI: http://rtcamp.com
  License: GPL
  Text Domain: rt_biz_export
 */

if ( ! defined( 'RT_BIZ_EXPORT_VERSION' ) ) {
	define( 'RT_BIZ_EXPORT_VERSION', '0.0.5' );
}
if ( ! defined( 'RT_BIZ_EXPORT_PATH' ) ) {
	define( 'RT_BIZ_EXPORT_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'RT_BIZ_EXPORT_URL' ) ) {
	define( 'RT_BIZ_EXPORT_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'RT_BIZ_EXPORT_PATH_TEMPLATES' ) ) {
	define( 'RT_BIZ_EXPORT_PATH_TEMPLATES', plugin_dir_path( __FILE__ ) . 'templates/' );
}
if ( !defined( 'RT_BIZ_EXPORT_TEXT_DOMAIN' ) ) {
	define( 'RT_BIZ_EXPORT_TEXT_DOMAIN', 'rt_biz_export' );
}

include_once RT_BIZ_EXPORT_PATH . 'app/lib/wp-helpers.php';

function rt_biz_export_include() {

//	include_once RT_BIZ_EXPORT_PATH . 'app/helper/rt-biz-functions.php';

	global $rtbe_app_autoload, $rtbe_models_autoload, $rtbe_abstract_autoload, $rtbe_modules_autoload, $rtbe_settings_autoload, $rtbe_notification_autoload;
	$rtbe_app_autoload = new RT_WP_Autoload( RT_BIZ_EXPORT_PATH . 'app/' );
//	$rtb_models_autoload = new RT_WP_Autoload( RT_BIZ_PATH . 'app/models/' );
//	$rtb_abstract_autoload = new RT_WP_Autoload( RT_BIZ_PATH . 'app/abstract/' );
//	$rtb_modules_autoload = new RT_WP_Autoload( RT_BIZ_PATH . 'app/modules/' );
//	$rtb_notification_autoload = new RT_WP_Autoload( RT_BIZ_PATH . 'app/notification/' );
//	$rtb_settings_autoload = new RT_WP_Autoload( RT_BIZ_PATH . 'app/settings/' );
}

function rt_biz_export_init() {

	rt_biz_export_include();

	global $rt_biz;
	$rt_biz = new Rt_Biz_Export();
}

add_action( 'rt_biz_init', 'rt_biz_export_init', 1 );
