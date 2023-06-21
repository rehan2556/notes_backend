<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/third_party/php-jwt/src/BeforeValidException.php';
require APPPATH.'/third_party/php-jwt/src/ExpiredException.php';
require APPPATH.'/third_party/php-jwt/src/JWT.php';
require APPPATH.'/third_party/php-jwt/src/SignatureInvalidException.php';
use \Firebase\JWT\JWT;

/*
* Firebase JWT
*/

/**
* Encodes a JSON Web Token, given a user's uid.
* @param user_uid The user's unique id.
*/
if (!function_exists('jwt_encode'))
{
	function jwt_encode($user_uid)
	{
		$date = new DateTime();
		$timestamp = $date->getTimestamp();

		/*
		* Put a hashed lpu (latest_pass_update) inside the token to be able to
		* invalidate tokens that were issued before the latest password update.
		*/
		$CI =& get_instance();
		$lpu = $CI->m_pro->get_single_column_as_string('user', array('user_uid' => $user_uid), 'last_pass_update');

		$token = array(
			"iat" => $timestamp,
			"nbf" => $timestamp,
			"data" => array(
				"uid" => $user_uid,
				"lpu" => md5($lpu)
			)
		);

		/**
		* IMPORTANT:
		* You must specify supported algorithms for your application. See
		* https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
		* for a list of spec-compliant algorithms.
		*/
		$jwt = JWT::encode($token, JWT_ENCRYPTION_KEY);

		return $jwt;
	}
}

/**
* Verifies a JSON Web Token.
* @return {
*   status: 0 (token_missing) | 1 (invalid_token) | 2 (invalid_token_lpu) | 3 (success).
*   data: { uid, lpu } | NULL
* }
*/
if (!function_exists('jwt_verify'))
{
	function jwt_verify($jwt)
	{
		if (empty($jwt)) {
			return [ 'status' => 0, 'data' => NULL ];
		}

		// Strip the 'Bearer' prefix
		if (substr($jwt, 0, 6) === 'Bearer') {
			$jwt = substr($jwt, 7, strlen($jwt));
		}

		try {
			$decoded = JWT::decode($jwt, JWT_ENCRYPTION_KEY, array('HS256'));

			/*
			* The token is valid (has not been tampered with). Now we need to make
			* sure that the token was issued after the latest password update.
			*/

			/*
			* NOTE: This will now be an object instead of an associative array. To get
			* an associative array, you will need to cast it as such:
			*/
			$decoded_array = (array) $decoded;
			$data_array = (array) $decoded_array['data'];

			$CI =& get_instance();
			$lpu = $CI->m_pro->get_single_column_as_string('user', array('user_uid' => $data_array['uid']), 'last_pass_update');

			if (md5($lpu) === $data_array['lpu']) // Check if lpu matches
			{
				return [ 'status' => 3, 'data' => [ 'uid' => $data_array['uid'], 'lpu' => $data_array['lpu'] ] ];
			}
			else
			{
				return [ 'status' => 2, 'data' => NULL ];
			}
		}
		catch (Exception $e)
		{
			return [ 'status' => 1, 'data' => NULL ];
		}
	}
}
