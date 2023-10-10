<?php
	/*
	Name: shortcode.php 
	Description: Used to create CF7 shortcodes, which can be used in the CF7 forms using the presented UI.
	*/

	/*
	Shortcode example: [flodesk_checkbox visible]
	Make the shortcode available to Contact Form 7
	*/
	function custom_add_shortcode_flodesk() {
		wpcf7_add_shortcode( 'flodesk_checkbox', 'flodesk_subscribe_checkbox', true );
	}
	add_action( 'wpcf7_init', 'custom_add_shortcode_flodesk' );

	/*
	Insert form elements into the Contact Form 7 form
	
	TODO: Enable custom checkbox label 
		  Allow user to specify it in the admin panel
	*/
	function flodesk_subscribe_checkbox( $atts ) {
		$label = get_option('flodesk_settings')['flodesk_label'];
		if (empty($label)) {
			$label = 'Subscribe to newsletter';
		}
		$tag = new WPCF7_FormTag( $atts );
		
		if ( $tag->name === 'hidden') {
			return '<label class="flodesk_checkbox" style="display: none;"><input type="checkbox" name="flodesk" id="flodesk" class="wpcf7_checkbox" style="margin-right: 10px;" checked>'.$label.'</label>';
		}
		return '<label class="flodesk_checkbox"><input type="checkbox" name="flodesk" id="flodesk" class="wpcf7_checkbox" style="margin-right: 10px;">'.$label.'</label>';
	}

?>