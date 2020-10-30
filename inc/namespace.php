<?php
/**
 * Bootstraps the Javascript Consent API.
 *
 * @package altis/consent
 */

namespace Altis\Consent\API;

defined( 'WP_CONSENT_API_URL' ) or define( 'WP_CONSENT_API_URL', plugin_dir_url( __FILE__ ) );
defined( 'WP_CONSENT_API_VERSION' ) or define( 'WP_CONSENT_API_VERSION', get_plugin_data()['Version'] ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '-' . time() : '';

/**
 * Get the plugin data from the file header.
 *
 * @return array Array of plugin file header values keyed by header name.
 * @see    get_file_data http://developer.wordpress.org/reference/functions/get_file_data/
 */
function get_plugin_data() : array {
	return get_file_data( __FILE__, [ 'Version' => 'Version' ], false );
}

/**
 * Enqueue and localize the javascript.
 */
function enqueue_api() {
	$min     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	$options = get_option( 'cookie_consent_options', [] );

	wp_enqueue_script( 'altis-consent-api', WP_CONSENT_API_URL . "src/wp-consent-api$min.js", [], WP_CONSENT_API_VERSION, true );

		// Get the cookie expiration or set a default of 30 days.
		$expiration = isset( $options['cookie_expiration'] ) ? $options['cookie_expiration'] : 30;

	/**
	 * When the consenttype (optin or optout) can be set dynamically, we can tell plugins to wait in the javascript until the consenttype has been determined.
	 *
	 * @param bool $waitfor_consent_hook Wait until consent has been given.
	 */
	$waitfor_consent_hook = apply_filters( 'wp_consent_api_waitfor_consent_hook', false );

	/**
	 * The consent cookie prefix. Defaults to wp_consent_.
	 *
	 * @param string $prefix The active cookie prefix.
	 */
	$prefix = apply_filters( 'wp_consent_cookie_prefix', 'wp_consent' );

	/**
	 * The active consent type. Defaults to optin.
	 *
	 * @param string $consent_type The active consent type.
	 */
	$consent_type = apply_filters( 'wp_get_consent_type', 'optin' );

	/**
	 * The list of active consent types.
	 *
	 * @param array $consent_types The available consent types.
	 */
	$consent_types = apply_filters( 'wp_consent_types', [ 'optin', 'optout' ] );

	// Send the variables and filterable values to the javascript consent API.
	wp_localize_script(
		'altis-consent-api',
		'consent_api',
		[
			'consent_type' => in_array( $consent_type, $consent_types, true ) ? $consent_type : 'optin',
			'waitfor_consent_hook' => (bool) $waitfor_consent_hook,
			'cookie_expiration' => intval( $expiration ),
			'cookie_prefix' => sanitize_text_field( $prefix ),
		]
	);
}
