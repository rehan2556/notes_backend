<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Description of Open
*
* @author asimriaz
*/

require APPPATH.'/libraries/NT_REST_Controller.php';

class User extends NT_REST_Controller
{

	function __construct()
	{
		// Construct our parent class
		parent::__construct(FALSE);  // Don't authorize requests

		// Load Helpers (If any)

		// Load models
	}

	// Using
	function register_post()
	{
		$first_name = $this->post('first_name');
		$last_name = $this->post('last_name');
		$email = $this->post('email');
		$user_name = $this->post('username');
		$password = $this->post('password');


		$valid = TRUE;

		if (empty($first_name) OR empty($last_name) OR empty($email) OR empty($user_name) OR empty($password))
		{
			$response = array('action' => 'print', 'message' => 'Oops, Request parameters are missing. Please fill in all require fields.');
			$response_code = REST_Controller::HTTP_BAD_REQUEST;
			$valid = FALSE;
		}

		if ($valid)
		{
			if (!valid_email($email))
			{
				$response = array('action' => 'print', 'message' => "Please enter valid email address.");
				$response_code = REST_Controller::HTTP_BAD_REQUEST;
				$valid = FALSE;
			}
		}

		if ($valid)
		{
			$result_email_user_name = is_username_email_available($email, $user_name);

			if ($result_email_user_name['is_email_taken'])
			{
				$response = array('action' => 'print', 'message' => "Email is already taken.");
				$response_code = REST_Controller::HTTP_BAD_REQUEST;
				$valid = FALSE;
			}

			if ($valid)
			{
				if ($result_email_user_name['is_user_name_taken'])
				{
					$response = array('action' => 'print', 'message' => "Username already taken.");
					$response_code = REST_Controller::HTTP_BAD_REQUEST;
					$valid = FALSE;
				}
			}
		}

		if ($valid)
		{
			if (!validate_password($password))
			{
				$response = array('action' => 'print', 'message' => "Password must be minimum eight characters including at least one capital letter and one special character.");
				$response_code = REST_Controller::HTTP_BAD_REQUEST;
				$valid = FALSE;
			}
		}

		if ($valid)
		{
            $user['user_uid'] = generate_UID();
			$user['username'] = $user_name;
			$user['password'] = password_hash($password, PASSWORD_BCRYPT);
			$user['first_name'] = $first_name;
			$user['last_name'] = $last_name;
			$user['email'] = $email;
			$user['create_on'] = current_date_time();

			// Create User
			$user_inserted_id = $this->m_pro->insert('user', $user);

			$return_data = array();
			$return_data['user']['email'] = $user['email'];
			$return_data['user']['login'] = $user['username'];
			$return_data['user']['first_name'] = $user['first_name'];
			$return_data['user']['last_name'] = $user['last_name'];
			$return_data['user']['token'] = jwt_encode($user['user_uid']);
			$return_data['user']['is_email_verified'] = 0;

			$response = array(
				'action' => "save",
				'data_to_save' => $return_data);
				$response_code = REST_Controller::HTTP_OK;
		}
			$this->response($response, $response_code);
	}

	// Using
	function login_post()
	{
		$user_name = $this->post('username');
		$password = $this->post('password');

		$valid = TRUE;

		if ( empty($user_name) OR empty($password))
		{
			$response = array('action' => 'alert', 'message' => 'Oops, Request parameters are missing. Please fill in all require fields.');
			$response_code = REST_Controller::HTTP_BAD_REQUEST;
			$valid = FALSE;
		}

		if ($valid)
		{
			$user = $this->m_pro->get_row('user', array('username' => $user_name)); // Get User

			if (is_array($user) && !empty($user))
			{
				if (!password_verify($password, $user['password']))
				{
					$response = array('action' => 'alert', 'message' => 'Password is incorrect.');
					$response_code = REST_Controller::HTTP_OK;
					$valid = FALSE;
				}
			}
			else
			{
				$this->session->set_flashdata('alert-error', 'User not found.');
				$response = array('action' => 'alert', 'message' => 'User not found.');
				$response_code = REST_Controller::HTTP_OK;
				$valid = FALSE;
			}
		}

		if ($valid)
		{

		$return_data = array();
		$return_data['email'] = $user['email'];
		$return_data['first_name'] = $user['first_name'];
		$return_data['last_name'] = $user['last_name'];
		$return_data['user_name'] = $user['username'];
		$return_data['token'] = jwt_encode($user['user_uid']);

		$response = array(
			'action' => "save",
			'data_to_save' => $return_data);
			$response_code = REST_Controller::HTTP_OK;
		}
		$this->response($response, $response_code);
	}
}
