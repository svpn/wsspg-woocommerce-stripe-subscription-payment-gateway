<?php
/**
 * Wsspg API
 *
 * Handles Stripe API requests, using wp_safe_remote_post().
 *
 * @since       1.0.0
 * @package     Wsspg
 * @subpackage  Wsspg/includes
 * @author      wsspg <wsspg@mail.com>
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright   2016 (c) http://wsspg.co
 */

if( ! defined( 'ABSPATH' ) ) exit; // exit if accessed directly.

/**
 * Wsspg API Class
 *
 * @since  1.0.0
 * @class  Wsspg_API
 */
class Wsspg_API {
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}
	
	/**
	 * Stripe API request handler.
	 *
	 * Returns an array with the request id and the response body,
	 * or null if the request failed for any reason.
	 *
	 * @since   1.0.0
	 * @param   string
	 * @param   string
	 * @param   array
	 * @param   string
	 * @return  object | null
	 */
	public static function request( $action, $key, $params = array(), $method = 'POST' ) {
		
		$url = WSSPG_PLUGIN_API.$action;
		$data = array(
			'method'   => $method,
			'headers'  => array( 'Authorization' => 'Basic '.base64_encode( "{$key}:" ) ),
			'body'     => $params,
		);
		$response = null;
		if( $method === 'POST' ) {
			$response = wp_safe_remote_post( $url, $data );
		} elseif( $method === 'GET' ) {
			$response = wp_safe_remote_get( $url, $data );
		} else {
			$response = wp_safe_remote_post( $url, $data );
		}
		if( is_wp_error( $response ) || !isset( $response  ) ) {
			Wsspg::log( sprintf(
				'[ %s ][ %s ]: %s',
				__( 'error', 'wsspg' ),
				$action,
				print_r( $response, true )
			) );
		} else {
			$response_headers  = $response['headers']->getAll();
			$response_body     = json_decode( $response['body'] );
			if( isset( $response_body->error ) ) {
				Wsspg::log( sprintf(
					'[ %s ][ %s ][ %s ]: %s',
					__( 'error', 'wsspg' ),
					$action,
					isset( $response_headers['request-id'] ) ? $response_headers['request-id'] : '',
					print_r( $response_body->error, true )
				) );
			} else {
				$response_body->request = $response_headers['request-id'];
				return $response_body;
			}
		}
		return null;
	}
}









