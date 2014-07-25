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
 * Description of class-rt-biz-export-format
 *
 * @author udit
 */
if ( ! class_exists( 'Rt_Biz_Export_Format' ) ) {

	abstract class Rt_Biz_Export_Format {

		/**
		 * Name/slug of the format
		 *
		 * @var string
		 */
		public static $name = null;

		/**
		 * File extension of the format
		 * Without a '.' (e.g. 'csv', not '.csv')
		 *
		 * @var string
		 */
		public static $extension = null;

		/**
		 * The filename to use for the exported file
		 *
		 * @var string
		 */
		public $filename;

		/**
		 * An array containing the header row labels
		 * for the data
		 *
		 * @var string
		 */
		public $header_row = array();

		/**
		 * An array containing rows of data
		 *
		 * @var string
		 */
		public $export_data = array();

		public abstract function convert_array_to_format( $header_row, $export_data );

		public function __construct( $header_row, $export_data ) {
			$this->header_row = $header_row;
			$this->export_data = $export_data;
		}

		public function send_file() {
			$this->filename = $this->get_filename();
			$this->send_headers();
			echo $this->convert_array_to_format( $this->header_row, $this->export_data ); //xss ok
			die();
		}

		public function send_headers() {
			$class = get_called_class();

			// disable caching
			$now     = gmdate( 'D, d M Y H:i:s' );
			$headers = array(
				'expires'             => 'Expires: 0',
				'cache_control'       => 'Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate',
				'last_modified'       => 'Last-Modified: {' . $now . '} GMT',
				'force_download'      => 'Content-Type: application/force-download',
				'octet_stream'        => 'Content-Type: application/octet-stream',
				'download'            => 'Content-Type: application/download',
				'content_disposition' => 'Content-Disposition: attachment;filename=' . $this->filename,
				'content_encoding'    => 'Content-Transfer-Encoding: binary',
			);

			$headers = apply_filters( 'rt_biz_data_exporter_download_headers', $headers, $this->filename, $now );
			$headers = apply_filters( 'rt_biz_data_exporter_download_headers_' . $class::$name, $headers, $this->filename, $now );

			foreach ( $headers as $header ) {
				header( $header );
			}

		}

		public function get_filename() {
			$class = get_called_class();

			$blogname = strtolower( get_site_option( 'site_name' ) );

			$timestamp = time();

			$filename = sanitize_file_name( $blogname . '_biz-entity-records_' . $timestamp . '.' . $class::$extension );

			return apply_filters( 'rt_biz_data_exporter_download_filename', $filename, $class::$name );
		}

	}

}