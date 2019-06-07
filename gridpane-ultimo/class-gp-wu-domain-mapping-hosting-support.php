<?php


class GP_WU_Domain_Mapping_Hosting_support extends WU_Domain_Mapping_Hosting_Support
{
	public function __construct() {

		/**
		 * GridPane.com Support
		 */
		if ($this->uses_gridpane()) {
			add_action('mercator.mapping.created', array($this, 'add_domain_gridpane'), 20);
			add_action('mercator.mapping.updated', array($this, 'add_domain_gridpane'), 20);
			add_action('mercator.mapping.deleted', array($this, 'remove_domain_from_gridpane'), 20);
		} // end if;

	} // end construct;

	/**
	 * Checks if this site is hosted on GridPane.com or not
	 *
	 * @return bool
	 */
	public function uses_gridpane() {
		return defined('WU_GRIDPANE') && WU_GRIDPANE;
	}

	/**
	 * Sends a request to Gridpane.com, with the right API key
	 *
	 * @param  string $endpoint Endpoint to send the call to
	 * @param  array  $data     Array containing the params to the call
	 * @return object
	 */
	public function send_gridpane_api_request($endpoint, $data = array(), $method = 'POST') {
		$post_fields = array(
			'timeout'     => 45,
			'blocking'    => true,
			'method'      => $method,
			'body'        => array_merge(array(
				'api_token'       => WU_GRIDPANE_API_KEY,
			), $data)
		);

		$response = wp_remote_request('https://my.gridpane.com/api/domain/' . $endpoint, $post_fields);

		if (!is_wp_error($response)) {
			$body = json_decode(wp_remote_retrieve_body($response), true);

			if (json_last_error() === JSON_ERROR_NONE) {
				return $body;
			}
		}

		return $response;
	}

	/**
	 * Add domain to GridPane.com
	 *
	 * @param  Mercator\Mapping $mapping
	 * @return void
	 */
	public function add_domain_gridpane($mapping) {
		$domain = $mapping->get_domain();

		if (!$this->uses_gridpane() || ! $domain) {
			return;
		}

		$this->send_gridpane_api_request('add-domain', array(
			'server_ip' => WU_GRIDPANE_SERVER_URL,
			'site_url' => WU_GRIDPANE_APP_ID,
			'domain_url' => $domain
		));
	}

	/**
	 * Removes a mapped domain from Gridpane.com
	 *
	 * @param  Mercator\Mapping $mapping
	 * @return void
	 */
	public function remove_domain_from_gridpane($mapping) {
		$domain = $mapping->get_domain();

		if (!$this->uses_gridpane() || ! $domain) {
			return;
		}

		$this->send_gridpane_api_request('delete-domain', array(
			'server_ip' => WU_GRIDPANE_SERVER_URL,
			'site_url' => WU_GRIDPANE_APP_ID,
			'domain_url' => $domain
		));
	}

}

if ( ! method_exists('WU_Domain_Mapping_Hosting_Support','uses_gridpane')) {

	new GP_WU_Domain_Mapping_Hosting_Support();

};