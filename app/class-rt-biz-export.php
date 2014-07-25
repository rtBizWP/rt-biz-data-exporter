<?php

/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-rt-biz-export
 *
 * @author udit
 */
if ( ! class_exists( 'Rt_Biz_Export' ) ) {

	class Rt_Biz_Export {

		public static $export_page_slug = 'rt-biz-export-data';

		/**
		 * Holds export formats
		 *
		 * @var array
		 */
		public static $export_formats = array();

		/**
		 * Holds export format labels
		 *
		 * @var array
		 */
		public static $export_format_labels = array();

		/**
		 * Holds admin notices messages
		 *
		 * @var array
		 */
		public static $messages = array();
		var $templateURL;

		public function __construct() {
			// Admin notices
			add_action( 'all_admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'admin_menu', array( $this, 'register_menu' ) );
			$this->templateURL = apply_filters( 'rt_biz_export_template_url', 'rt_biz_export/' );
			add_action( 'init', array( $this, 'process_export_form' ) );
			$this->get_export_formats();
		}

		/**
		 * Returns a list of available export formats
		 *
		 * @return void
		 */
		public function get_export_formats() {

			$classes = array();
			$class = "Rt_Biz_Export_CSV";
			$classes[ $class::$name ] = $class;

			self::$export_formats = apply_filters( 'rt_biz_data_exporter_formats', $classes );

			foreach ( self::$export_formats as $format ) {
				// Check if the connectors extends the WP_Stream_Connector class, if not skip it
				if ( ! is_subclass_of( $format, 'Rt_Biz_Export_Format' ) ) {
					self::$messages[] = sprintf(
							__( "%s class wasn't loaded because it doesn't extends the %s class." ), $format, 'Rt_Biz_Export_Format'
					);

					continue;
				}

				// Store connector label
				if ( ! in_array( $format::$name, self::$export_format_labels ) ) {
					self::$export_format_labels[ $format::$name ] = $format::get_label();
				}
			}

			/**
			 * This allow to perform action after all formats are registered
			 *
			 * @param array all registered formats labels array
			 */
			do_action( 'rt_biz_data_exporter_after_formats_registration', self::$export_format_labels );
		}

		/**
		 * Display all messages on admin board
		 *
		 * @return void
		 */
		public static function admin_notices() {
			foreach ( self::$messages as $message ) {
				echo wp_kses_post( $message );
			}
		}

		function process_export_form() {
			if ( ! isset( $_POST[ 'rt_biz_export_form' ] ) || empty( $_POST[ 'rt_biz_export_form' ] ) ) {
				return;
			}

			if ( empty( $_POST[ 'rt_biz_export_file_format' ] ) ) {
				global $rt_biz_export_errors;
				$rt_biz_export_errors[] = __( 'No File Format is chosen for export. Please choose one.' );
				return;
			}

			$file_format = $_POST[ 'rt_biz_export_file_format' ];

			if ( empty( $_POST[ 'rt_biz_export_entity' ] ) ) {
				global $rt_biz_export_errors;
				$rt_biz_export_errors[] = __( 'No Entity is chosen for export. Please choose one.' );
				return;
			}

			$entity = $_POST[ 'rt_biz_export_entity' ];
			$header_row = array();
			$export_data = array();
			switch ( $entity ) {
				case rt_biz_get_person_post_type():
					// Fetch Person Data According to Filters
					$person_export = new Rt_Biz_Export_Person();
					$header_row = $person_export->header_row;
					$export_data = $person_export->export_data;
					break;
				case rt_biz_get_organization_post_type():
					// Fetch Person Data According to Filters
					echo 'organization';
					break;
				default:
					do_action( 'rt_biz_process_export_form' );
					break;
			}

			foreach ( self::$export_formats as $key => $value ) {
				if ( $file_format === $key ) {
					$exporter = new $value( $header_row, $export_data );
					$exporter->send_file();
					break;
				}
			}
		}

		function register_menu() {
			$menu_label = Rt_Biz_Settings::$settings[ 'menu_label' ];
			$menu_title = $menu_label . ' ' . __( 'Data Exporter' );
			add_submenu_page( Rt_Biz::$dashboard_slug, $menu_title, $menu_title, rt_biz_get_access_role_cap( RT_BIZ_TEXT_DOMAIN, 'admin' ), self::$export_page_slug, array( $this, 'export_page' ) );
		}

		function export_page() {
			rt_biz_export_get_template( 'export-form.php' );
		}

	}

}