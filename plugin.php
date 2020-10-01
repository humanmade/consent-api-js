<?php
/**
 * WordPress Consent API JS
 *
 * Forked from WP Consent API.
 *
 * Copyright 2020 Rogier Lankhorst and the WordPress Core Privacy team.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://www.gnu.org/licenses/.
 *
 * @package altis/consent
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Plugin Name:       WP Consent API JS
 * Plugin URI:        https://github.com/humanmade/consent-api-js
 * Description:       Forks the JavaScript Consent API in rlankhorst/wp-consent-level-api. Consent Level API to read and register the current consent level for cookie management and improving compliance with privacy laws.
 * Version:           1.0.2
 * Author:            RogierLankhorst and Human Made
 * Author URI:        https://humanmade.com
 * Requires at least: 5.0
 * Requires PHP:      5.6
 * License:           GPL2+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'WP_CONSENT_API_URL' ) or define( 'WP_CONSENT_API_URL', plugin_dir_url( __FILE__ ) );
defined( 'WP_CONSENT_API_VERSION' ) or define( 'WP_CONSENT_API_VERSION', get_plugin_data()['Version'] ) . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '-' . time() : '';

require_once __DIR__ . '/inc/namespace.php';

// Load the Consent API.
Altis\Consent\enqueue_api();
