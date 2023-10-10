<?php
	/*
	Name: cf7.php
	Description: Extension of the Contact Form 7 hooks. Extend the hooks in order to trigger the Flodesk API
	*/

	/*
	Load API functions
	*/
	require_once('api.php');

	/*
	Enabled CF7 debug mode (if you havent setup your mailing service yet)
	*/
	function wpcf7_prevent_email($cf7) {
		// get the contact form object
		$wpcf = WPCF7_ContactForm::get_current();

		if (get_option('flodesk_settings')['flodesk_stop_email'] === 'on') {
			return true;
		}

		return $cf7;
	} 

	add_filter( 'wpcf7_skip_mail', 'wpcf7_prevent_email', 10, 2);

	/*
	Trigger API calls before CF7 successfully have sent an email
	*/
	function wpcf7_custom_before_email($cf7) {
		if (get_option('flodesk_settings')['flodesk_trigger'] != 'wpcf7_before_send_mail') {
			return;
		}

		$wpcf7 = WPCF7_ContactForm::get_current();
		$submission = WPCF7_Submission::get_instance();

		if ($submission) {
			$data = $submission->get_posted_data();

			if ($data['flodesk'] == 'on') {
				createSubscriber($data['your-email'], $data['your-name'], get_option('flodesk_settings')['flodesk_key']);
			}
			return;
		}
		return;
	}
	add_action('wpcf7_before_send_mail', 'wpcf7_custom_before_email', 10, 2);

	/*
	Trigger API calls when CF7 successfully have sent an email
	*/
	function wpcf7_custom_after_email($cf7) {
		if (get_option('flodesk_settings')['flodesk_trigger'] != 'wpcf7_mail_sent') {
			return get_option('flodesk_settings')['flodesk_trigger'];
		}

		$wpcf7 = WPCF7_ContactForm::get_current();
		$submission = WPCF7_Submission::get_instance();

		if ($submission) {
			$data = $submission->get_posted_data();

			if ($data['flodesk'] == 'on') {
				createSubscriber($data['your-email'], $data['your-name'], get_option('flodesk_settings')['flodesk_key']);
			}
			return;
		}
		return;
	}
	add_filter('wpcf7_mail_sent', 'wpcf7_custom_after_email', 10, 2);

?>