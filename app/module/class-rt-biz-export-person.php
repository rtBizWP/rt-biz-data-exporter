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
 * Description of class-rt-biz-export-person
 *
 * @author udit
 */
if ( ! class_exists( 'Rt_Biz_Export_Person' ) ) {

	class Rt_Biz_Export_Person extends Rt_Biz_Export_Entity {

		public function __construct() {
			$this->post_type = rt_biz_get_person_post_type();
			$rt_biz_attributes_model = new RT_Attributes_Model();
			$rt_biz_attributes_relationship_model = new RT_Attributes_Relationship_Model();
			$relations = $rt_biz_attributes_relationship_model->get_relations_by_post_type( $this->post_type );
			$attributes = array();
			foreach ( $relations as $r ) {
				$attr = $rt_biz_attributes_model->get_attribute( $r->attr_id );
				if ( $attr->attribute_store_as == 'taxonomy' ) {
					$attributes[] = $attr;
				}
			}
			$this->attributes = $attributes;

			parent::__construct();
		}

		function build_query_args() {
			$args = parent::build_query_args();

			if ( isset( $_POST[ 'rt_biz_export_person_attr' ] ) ) {
				foreach ( $_POST['rt_biz_export_person_attr'] as $tax => $term ) {
					if ( ! empty( $term ) ) {
						$args['tax_query'][] = array(
							'taxonomy' => $tax,
							'terms' => $term,
						);
					}
				}
			}

			return $args;
		}

	}

}