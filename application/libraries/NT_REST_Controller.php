<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
* Description of VN_REST_Controller
*
* @author asimriaz
*/
require APPPATH.'/libraries/REST_Controller.php';

class NT_REST_Controller extends REST_Controller
{
	protected $CI;

	public function __construct($authorize = TRUE)
	{
		// Construct our parent class
		parent::__construct();

		$this->CI =& get_instance();

		$this->jwt = array();
		if ($authorize)
		{
			$this->_authorize_request();
		}
	}

	/**
	* Authorizes a request.
	*/
	private function _authorize_request()
	{
		$headers = $this->input->request_headers();
		$authorization_header = isset($headers['Authorization-Token']) ? $headers['Authorization-Token'] : NULL;

		$jwt = jwt_verify($authorization_header);
		$status = $jwt['status'];
		if ($status === 3)
		{
			/*
			* Set the user_uid of the requester. This uid can be used by the API controller.
			*/
			if (!empty($jwt['data']))
			{
				$this->jwt['user_uid'] = $jwt['data']['uid'];
			}
		}
		else
		{
			switch ($status)
			{
				case 0:
				case 1:
				$message = 'You do not have permission to perform this operation. If the problem persists, try logging out and then logging in again.';
				$action = 'alert';
				break;
				case 2:
				$message = 'The password for the account has changed. Please log out and then log in again.';
				$action = 'alert';
				break;
			}
			$response = array(
				'action' => $action,
				'message' => $message
			);
			$response_code = REST_Controller::HTTP_UNAUTHORIZED;
			$this->response($response, $response_code);
			exit;
		}
	}
}
