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
 * Description of class-rt-biz-export-entity
 *
 * @author udit
 */
if ( ! class_exists( 'Rt_Biz_Export_Entity' ) ) {

	class Rt_Biz_Export_Entity {

		public $header_row;
		public $export_data;
		public $post_type;
		public $attributes;
		public $list_table;

		public function __construct() {
			$this->load_data();
		}

		public function load_data() {
			require_once( ABSPATH . 'wp-admin/includes/post.php' );
			require_once( ABSPATH . 'wp-admin/includes/screen.php' );
			require_once( ABSPATH . 'wp-admin/includes/template.php' );
			require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			require_once( ABSPATH . 'wp-admin/includes/class-wp-posts-list-table.php' );
			$this->list_table = new WP_Posts_List_Table( array( 'screen' => $this->post_type ) );
			$this->list_table->prepare_items();

			$rows = apply_filters( 'rt_biz_data_exporter_records', $this->get_records() );
			$columns = apply_filters( 'rt_biz_data_exporter_columns', $this->list_table->get_columns(), $rows );
			$excluded_columns = array(
				'cb',
				'id',
				'date',
				'author',
				'comments',
			);
			$columns = array_diff_key( $columns, array_flip( $excluded_columns ) );

			$this->header_row = $this->get_header_row( $columns );
			$this->export_data = $this->get_export_data( $rows, $columns );
		}

		function get_records() {
			$args = array(
				'post_type' => $this->post_type,
				'post_status' => 'any',
			);

			// Parse sorting params
//			if ( ! $order = rt_biz_filter_input( INPUT_GET, 'order' ) ) {
			$order = 'DESC';
//			}
//			if ( ! $orderby = rt_biz_filter_input( INPUT_GET, 'orderby' ) ) {
			$orderby = '';
//			}
			$args[ 'order' ] = $order;

			$args[ 'paged' ] = $this->list_table->get_pagenum();

			if ( ! isset( $args[ 'posts_per_page' ] ) ) {
				$args[ 'posts_per_page' ] = $this->list_table->get_items_per_page( 'edit_' . $this->post_type . '_per_page', 20 );
			}

			$query = new WP_Query( $args );

			return $query->posts;
		}

		public function get_header_row( $columns ) {
			$header_row = array();

			foreach ( $columns as $column_name => $column_label ) {
				$header_row[] = $column_name;
			}

			return apply_filters( 'rt_biz_data_exporter_header_row', $header_row );
		}

		public function get_export_data( $rows, $columns ) {
			$export_data = array();

			foreach ( $rows as $row ) {
				foreach ( $columns as $column_name => $column_label ) {
					$export_data[ $row->ID ][ $column_name ] = $this->get_column_value( $row, $column_name );
				}
			}

			return apply_filters( 'rt_biz_data_exporter_export_data', $export_data );
		}

		public function get_column_value( $item, $column_name ) {

			global $rt_biz_rt_attributes;

			switch ( $column_name ) {
				case 'title':
					$out = $item->post_title;
					break;
				default:

					$flag = false;
					foreach ( $this->attributes as $attr ) {

						if ( $column_name == $attr->attribute_name ) {
							$terms = wp_get_post_terms( $item->ID, $rt_biz_rt_attributes->get_taxonomy_name( $attr->attribute_name ) );
							if ( ! $terms instanceof WP_Error ) {
								$values = array();
								foreach ( $terms as $t ) {
									$values[] = $t->name;
								}
								$out = implode( ' , ', $values );
							} else {
								$out = '-';
							}
							$flag = true;
							break;
						}
					}

					/**
					 * This filter allows for the addition of exported data under the specified column ($column_name)
					 *
					 * @param  string  $column_name   Array key of the column
					 * @param  string  $column_title  Title of the column
					 * @param  obj     $item          Contents of the row
					 */
					if ( ! $flag ) {
						$out = apply_filters( 'rt_biz_data_exporter_column_' . $column_name, '', $column_name, $item );
					}
			}

			return apply_filters( 'rt_biz_data_exporter_column_value', $out, $column_name, $item );
		}

	}

}