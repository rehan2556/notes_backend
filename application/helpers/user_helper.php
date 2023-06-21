<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('is_username_email_available'))
{
	function is_username_email_available($email, $user_name)
	{
		$CI =& get_instance();

		$CI->db->where('email', $email);
		$CI->db->from('user');
		$result1 = $CI->db->count_all_results();
		$email_taken = $result1 ? TRUE : FALSE;

		$CI->db->where('username', $user_name);
		$CI->db->from('user');
		$result2 = $CI->db->count_all_results();
		$user_name_taken = $result2 ? TRUE : FALSE;

		$result = array(
			'is_email_taken' => $email_taken,
			'is_user_name_taken' => $user_name_taken
		);

		return $result;
	}
}

if(!function_exists('validate_password'))
{
	function validate_password($password)
	{
		$uppercase = preg_match('@[A-Z]@', $password);
		//$lowercase = preg_match('@[a-z]@', $string);
		$number    = preg_match('@[0-9]@', $password);
		$special_char = preg_match('@[%\@!#&*<>_/()?;]@', $password); //!@#$%^&*

		if(!$uppercase || !$special_char || !$number || strlen($password) < 8)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
}
