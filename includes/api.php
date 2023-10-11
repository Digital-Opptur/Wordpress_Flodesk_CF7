<?php
	/*
	Name: api.php
	Description: Functions file used in order to interact with the Flodesk API
	*/

	/*
	Test API credentials
	*/
	function validateCredentials ($key) {
		/*
		Run GET against the REST API to fetch segments
		Endpoint:https://api.flodesk.com/v1/segments
		*/
		$data = wp_remote_post('https://api.flodesk.com/v1/subscribers', array(
			'headers'     => array(
				'Authorization' => 'Basic '.base64_encode($key),
				'User-Agent' => 'Digital Opptur - Nettside',
				'Content-Type' => 'application/json'
			),
			'method'      => 'GET',
			'data_format' => 'body',
		));
		
		/*
		Return JSON result
		*/
		return json_decode($data['body']);
	}

	/*
	Create new subscriber
	*/
	function createSubscriber ($email, $name, $key) {    
		/*
		Setup the body object being pushed to create or update the user
		*/
		$body = [
			'email' => $email,
			'first_name' => explode(' ', $name)[0],
			'last_name' => explode(' ', $name)[1],
		];

		/*
		Run POST against the REST API to subscribe the user

		Endpoint: https://api.flodesk.com/v1/subscribers
		*/
		$data = wp_remote_post('https://api.flodesk.com/v1/subscribers', array(
			'headers'     => array(
				'Authorization' => 'Basic '.base64_encode($key),
				'User-Agent' => 'Digital Opptur - Nettside',
				'Content-Type' => 'application/json'
			),
			'body'        => json_encode($body),
			'method'      => 'POST',
			'data_format' => 'body',
		));

		/*
		Check to see if segmentation selection is enabled in the admin dashboard
		*/
		$segment = get_option('flodesk_settings')['flodesk_segment'];

		/*
		Setup the body object being pushed to update the segment of the user
		*/
		$body = [
			'segment_ids' => [$segment]
		];

		/*
		Run POST against the REST API to set the segment of the user
		if it's enabled in the dashboard

		Endpoint: https://api.flodesk.com/v1/subscribers/{id_or_email}/segments
		*/
		$final = wp_remote_post('https://api.flodesk.com/v1/subscribers/'.$email.'/segments', array(
			'headers'     => array(
				'Authorization' => 'Basic '.base64_encode($key),
				'User-Agent' => 'Digital Opptur - Nettside',
				'Content-Type' => 'application/json'
			),
			'body'        => json_encode($body),
			'method'      => 'POST',
			'data_format' => 'body',
		));

		/*
		Return JSON result
		*/
		return json_decode($final['body']);
	}

	/*
	Fetch subscribers
	*/
	function fetchSubscribers ($key) {
		/*
		Run GET against the REST API to fetch subscribers
		Endpoint:https://api.flodesk.com/v1/subscribers
		*/
		$data = wp_remote_post('https://api.flodesk.com/v1/subscribers', array(
			'headers'     => array(
				'Authorization' => 'Basic '.base64_encode($key),
				'User-Agent' => 'Digital Opptur - Nettside',
				'Content-Type' => 'application/json'
			),
			'method'      => 'GET',
			'data_format' => 'body',
		));

		/*
		Return JSON result
		*/
		return json_decode($data['body']);
	}

	/*
	Fetch segments
	*/
	function fetchSegments ($key) {
		/*
		Run GET against the REST API to fetch segments
		Endpoint:https://api.flodesk.com/v1/segments
		*/
		$data = wp_remote_post('https://api.flodesk.com/v1/segments', array(
			'headers'     => array(
				'Authorization' => 'Basic '.base64_encode($key),
				'User-Agent' => 'Digital Opptur - Nettside',
				'Content-Type' => 'application/json'
			),
			'method'      => 'GET',
			'data_format' => 'body',
		));
		
		/*
		Return JSON result
		*/
		return json_decode($data['body']);
	}

	/*
	Fetch segments
	*/
	function fetchCustomFields ($key) {
		/*
		Run GET against the REST API to fetch segments
		Endpoint:https://api.flodesk.com/v1/segments
		*/
		$data = wp_remote_post('https://api.flodesk.com/v1/custom-fields', array(
			'headers'     => array(
				'Authorization' => 'Basic '.base64_encode($key),
				'User-Agent' => 'Digital Opptur - Nettside',
				'Content-Type' => 'application/json'
			),
			'method'      => 'GET',
			'data_format' => 'body',
		));
		
		/*
		Return JSON result
		*/
		return json_decode($data['body']);
	}
?>