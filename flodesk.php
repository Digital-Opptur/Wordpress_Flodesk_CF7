<?php
	/*
	Plugin Name:  Contact Form 7 - Flodesk
	Plugin URI:   https://flodesk.com
	Description:  Simple plugin to use with CF7, adding new subscribers to flodesk
	Version:      1.1
	Author:       Digital Opptur
	Author URI:   https://digitalopptur.no
	License:      GPL2
	License URI:  https://www.gnu.org/licenses/gpl-2.0.html
	*/

	/*
	Register the flodesk CF7 shortcode
	*/
	require_once 'includes/shortcode.php';

	/*
	Require REST API functions
	*/
	require_once 'includes/api.php';

	/*
	Include custom Contact Form 7 functions
	*/
	require 'includes/cf7.php';

	/*
	Setup menu and admin items
	*/
	add_action('admin_menu', 'flodesk_setup_menu');

	function flodesk_setup_menu(){
		add_submenu_page( 'wpcf7',
			__( 'flodesk', 'contact-form-7' ),
			__( 'flodesk', 'contact-form-7' ),
			'wpcf7_edit_contact_forms', 'wpcf7-flodesk',
			'flodesk_init'
		);
	}

	/*
	Admin panel UI & settings
	*/
	function flodesk_init(){
		?>
		<div class="wrapper">
			<link rel="stylesheet" href="<?php echo plugin_dir_url(__DIR__).'/flodesk/includes/css/flodesk.css' ?>" />
			<form action="options.php" method="post">	
				<div class="wrapper-container">
					<div class="wrapper-header">
						<h1><b>flodesk</b></h1>
						<input type="submit" value="Save" />
					</div>
					<?php
						settings_fields( 'flodesk_settings' );
						do_settings_sections( __FILE__ );

						$options = get_option( 'flodesk_settings' ); ?>
						<table class="form-table" style="width: 100%;">
							<tr>
								<th scope="row">
									API Key <br>
									
										<?php
											$validate = validateCredentials($options['flodesk_key']);
											if ($validate->code) {
												echo '<span class="fd-invalid">Status: Invalid</span>';
											} else {
												echo '<span class="fd-valid">Status: Valid</span>';
											}
										?>
									
								</th>
								<td>
									<fieldset>
										<label style="width: 100%;">
											<input name="flodesk_settings[flodesk_key]" type="password" id="flodesk_key" value="<?php echo (isset($options['flodesk_key']) && $options['flodesk_key'] != '') ? $options['flodesk_key'] : ''; ?>" style="width: 100%;"/>
											<br />
											<span class="description">Read more about <a href="https://help.flodesk.com/en/articles/8128775-about-api-keys" target="_blank">getting your API key</a></span>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									Custom label
								</th>
								<td>
									<fieldset>
										<label style="width: 100%;">
											<input name="flodesk_settings[flodesk_label]" type="text" id="flodesk_key" value="<?php echo (isset($options['flodesk_label']) && $options['flodesk_label'] != '') ? $options['flodesk_label'] : ''; ?>" style="width: 100%;"/>
											<br />
											<span class="description">Change the default "Subscribe to newsletter" to something else.</span>
										</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">Enable debug mode?</th>
								<td>
									<fieldset>
										<label>
										<input type="checkbox" name="flodesk_settings[flodesk_stop_email]" id="flodesk_stop_email" <?php echo ((isset($options['flodesk_stop_email']) && $options['flodesk_stop_email'] != '') ? 'checked' : ''); ?> />
											<small>Enable if you are not using the email functions of Contact Form 7</small>
											</label>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">When to trigger</th>
								<td>
									<fieldset>
										<select value="before_email" name="flodesk_settings[flodesk_trigger]" id="flodesk_trigger">
											<option value="wpcf7_mail_sent" <?php echo ((isset($options['flodesk_trigger']) && $options['flodesk_trigger'] == 'wpcf7_mail_sent') ? 'selected' : ''); ?> >After email sent</option>
											<option value="wpcf7_before_send_mail" <?php echo ((isset($options['flodesk_trigger']) && $options['flodesk_trigger'] == 'wpcf7_before_send_mail') ? 'selected' : ''); ?> >Before email sent</option>
										</select>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									Add users to segment
								</th>
								<td>
									<fieldset>
										<select name="flodesk_settings[flodesk_segment]" id="flodesk_segment">
											<option value=""></option>
											<?php 
												$segments = fetchSegments($options['flodesk_key']);
												foreach ($segments->data as $value) {
											?>
												<option value="<?php echo $value->id; ?>" <?php echo ($options['flodesk_segment'] == $value->id) ? 'selected' : ''; ?>><?php echo $value->name; ?></option>
											<?php

												}
											?>
										</select>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th scope="row">
									Available custom fields
								</th>
								<td>
									<fieldset>
										<ul>
											<?php 
												$customfields = fetchCustomFields($options['flodesk_key']);
												if (!$customfields->data) { 
												?>
													No custom fields found
												<?php 
												} else {
													foreach ($customfields->data as $value) {
													?>
														<li><?php echo $value->label; ?></li>
													<?php

													}
												}
											?>
											
										</ul>
									</fieldset>
								</td>
							</tr>
							<tr>
								<th>
									Developed by
								</th>
								<td>
									<a href="https://digitalopptur.no" target="_blank">
										<img src="<?php echo plugin_dir_url(__DIR__).'/flodesk/includes/assets/do_logo.png'; ?>" height="20" />
									</a>
								</td>
							</tr>
						</table>
				</div>
			</form>
		</div>
		<div class="wrapper-footer">
			<span>Found a bug, or you want to request a feature? Checkout the <a href="https://github.com/Digital-Opptur/Wordpress_Flodesk_CF7/issues" target="_blank">GitHub repository</a></span>
		</div>

	<?php
	}

	/*
	Register plugin settings
	*/
	add_action('admin_init', 'flodesk_register_settings');
	function flodesk_register_settings(){
		register_setting('flodesk_settings', 'flodesk_settings', 'flodesk_settings_validate');
	}

	/*
	Validate the fields
	*/
	function flodesk_settings_validate($args){
		$validateKey = validateCredentials($args['flodesk_key']);
		if ($validateKey->code === 'unknown') {
			$args['flodesk_key'] = '';
			add_settings_error('flodesk_settings', 'flodesk_invalid_key', 'Please enter a valid API key!', $type = 'error');  
			return $args;
		}
		
		add_settings_error('flodesk_settings', 'flodesk_valid_key', 'Settings updated', $type = 'updated');   
		return $args;
	}

	/*
	Admin notices
	*/
	function flodesk_admin_notices(){
	   settings_errors();
	}
	add_action('admin_notices', 'flodesk_admin_notices');

?>