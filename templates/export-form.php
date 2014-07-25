<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$menu_label = Rt_Biz_Settings::$settings[ 'menu_label' ];
$person_labels = rt_biz_get_person_labels();
$person_post_type = rt_biz_get_person_post_type();
$person_meta_fields = rt_biz_get_person_meta_fields();
$organization_labels = rt_biz_get_organization_labels();
$organization_post_type = rt_biz_get_organization_post_type();
$organization_meta_fields = rt_biz_get_organization_meta_fields();
global $rt_biz_rt_attributes;
$rt_biz_attributes_model = new RT_Attributes_Model();
$rt_biz_attributes_relationship_model = new RT_Attributes_Relationship_Model();
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2><?php echo $menu_label . ' ' . __( 'Data Exporter' ); ?></h2>
	<div class="rt-biz-export-container">
		<?php
		global $rt_biz_export_errors;
		if ( ! empty( $rt_biz_export_errors ) ) {
			foreach ( $rt_biz_export_errors as $e ) {
				echo '<div class="error"><p>' . $e . '</p></div>';
			}
		}
		?>
		<form method="POST">
			<input type="hidden" name="rt_biz_export_form" value="1" />
			<div class="rt-biz-export-person">
				<label><input type="radio" name="rt_biz_export_entity" required="required" value="<?php echo $person_post_type; ?>" /> <?php echo $person_labels[ 'name' ]; ?></label>
				<div class="rt-biz-export-attributes">
					<label><?php _e( 'Attributes : ' ); ?></label>
					<?php
					$relations = $rt_biz_attributes_relationship_model->get_relations_by_post_type( $person_post_type );
					foreach ( $relations as $r ) {
						$attr = $rt_biz_attributes_model->get_attribute( $r->attr_id );
						if ( $attr->attribute_store_as == 'taxonomy' ) {
						?>
						<label><?php echo $attr->attribute_label.' : '; ?>
						<?php
							$tax = get_taxonomy( $rt_biz_rt_attributes->get_taxonomy_name( $attr->attribute_name ) );
							wp_dropdown_categories( array(
								'show_option_all' => __( "Show All {$tax->label}" ),
								'taxonomy' => $rt_biz_rt_attributes->get_taxonomy_name( $attr->attribute_name ),
								'name' => 'rt_biz_export_person_attr['.$rt_biz_rt_attributes->get_taxonomy_name( $attr->attribute_name ).']',
								'orderby' => 'name',
								'hierarchical' => true,
								'depth' => 3,
								'show_count' => false, // Show # listings in parens
								'hide_empty' => true, // Don't show businesses w/o listings
							) );
						?>
						</label>
							<?php
						}
					}
					?>
				</div>
				<div class="rt-biz-export-meta">
					<label><?php _e( 'Meta Fields : ' ); ?></label>
					<?php foreach ( $person_meta_fields as $m ) { ?>
						<label><input type="checkbox" name="rt_biz_export_person_meta[]" value="<?php echo $m[ 'key' ]; ?>" /> <?php echo $m[ 'text' ]; ?></label>
					<?php }
					?>
				</div>
			</div>
			<div class="rt-biz-export-organization">
				<label><input type="radio" name="rt_biz_export_entity" required="required" value="<?php echo $organization_post_type; ?>" /> <?php echo $organization_labels[ 'name' ]; ?></label>
				<div class="rt-biz-export-attributes">
					<label><?php _e( 'Attributes : ' ); ?></label>
					<?php
					$relations = $rt_biz_attributes_relationship_model->get_relations_by_post_type( $organization_post_type );
					foreach ( $relations as $r ) {
						$attr = $rt_biz_attributes_model->get_attribute( $r->attr_id );
						if ( $attr->attribute_store_as == 'taxonomy' ) {
						?>
						<label><?php echo $attr->attribute_label.' : '; ?>
						<?php
							$tax = get_taxonomy( $rt_biz_rt_attributes->get_taxonomy_name( $attr->attribute_name ) );
							wp_dropdown_categories( array(
								'show_option_all' => __( "Show All {$tax->label}" ),
								'taxonomy' => $rt_biz_rt_attributes->get_taxonomy_name( $attr->attribute_name ),
								'name' => 'rt_biz_export_organization_attr['.$rt_biz_rt_attributes->get_taxonomy_name( $attr->attribute_name ).']',
								'orderby' => 'name',
								'hierarchical' => true,
								'depth' => 3,
								'show_count' => false, // Show # listings in parens
								'hide_empty' => true, // Don't show businesses w/o listings
							) );
						?>
						</label>
						<?php }
					}
					?>
				</div>
				<div class="rt-biz-export-meta">
					<label><?php _e( 'Meta Fields : ' ); ?></label>
					<?php foreach ( $organization_meta_fields as $m ) { ?>
						<label><input type="checkbox" name="rt_biz_export_organization_meta[]" value="<?php echo $m[ 'key' ]; ?>" /> <?php echo $m[ 'text' ]; ?></label>
					<?php }
					?>
				</div>
			</div>
			<div class="rt-biz-export-file-format">
				<label><?php _e( 'File Format : ' ); ?></label>
				<?php foreach ( Rt_Biz_Export::$export_formats as $key => $value ) { ?>
					<label><input type="radio" name="rt_biz_export_file_format" required="required" value="<?php echo $key; ?>" /> <?php echo Rt_Biz_Export::$export_format_labels[ $key ]; ?></label>
				<?php } ?>
			</div>
			<input class="button-primary" type="submit" value="<?php _e( 'Export' ); ?>" />
		</form>
	</div>
</div>
