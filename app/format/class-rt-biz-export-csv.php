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
 * Description of class-rt-biz-export-csv
 *
 * @author udit
 */
if ( ! class_exists( 'Rt_Biz_Export_CSV' ) ) {

	class Rt_Biz_Export_CSV extends Rt_Biz_Export_Format {

		/**
		 * Name/slug of the format
		 *
		 * @var string
		 */
		public static $name = 'csv';

		/**
		 * File extension of the format
		 * Without a '.' (e.g. 'csv', not '.csv')
		 *
		 * @var string
		 */
		public static $extension = 'csv';

		/**
		 * Prefix for the Meta column name
		 *
		 * @var string
		 */
		public $meta_name_prefix = 'meta';

		/**
		 * Separator used in the header row for meta items
		 *
		 * @var string
		 */
		public $meta_separator = ':';

		public function __construct( $header_row, $export_data ) {
			parent::__construct( $header_row, $export_data );
		}

		/**
		 * Return translated format label
		 *
		 * @return string Translated context label
		 */
		public static function get_label() {
			return __( 'CSV' );
		}

		public function header_row_id( $header_row ) {
			return array_merge(
				array( 'id' => __( 'ID' ) ),
				$header_row
			);
		}

		public function export_data_id( $export_data ) {
			foreach ( $export_data as $key => $row ) {
				$export_data[ $key ] = array_merge(
					array( 'id' => $key ),
					$row
				);
			}
			return $export_data;
		}

		public function convert_array_to_format( $header_row, $export_data ) {
			if ( 0 === count( $export_data ) ) {
				return '';
			}

			ob_start();

			$df = fopen( 'php://output', 'w' );

			// Header row
			fputcsv( $df, $header_row );

			// Stream activity matrix
			foreach ( $export_data as $key => $row ) {
				fputcsv( $df, $row );
			}

			fclose( $df );

			return ob_get_clean();
		}
	}

}
