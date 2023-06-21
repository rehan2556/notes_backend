<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
* Example
*
* This is an example of a few basic user interaction methods you could use
* all done with a hardcoded array.
*
* @package		CodeIgniter
* @subpackage	Rest Server
* @category	Controller
* @author		Phil Sturgeon
* @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/NT_REST_Controller.php';

class Note extends NT_REST_Controller
{
	var $logged_in_user_uid;
	// var $is_latest_tnc_accepted;

	function __construct()
	{
		// Construct our parent class
		parent::__construct(); // Do Authrization

		$this->logged_in_user_uid = $this->jwt["user_uid"];

		// Load models
	}

    function note_post() 
    {
        $user_uid = $this->logged_in_user_uid;
        $title = $this->post('title');
        $description = $this->post('description');
        $valid = TRUE;

        if (empty($user_uid) OR empty($title))
		{
			$response = array('action' => 'alert',
							  'message' => "Oops, Request parameters are missing. Please fill in all require fields.");
			$response_code = REST_Controller::HTTP_BAD_REQUEST;
			$valid = FALSE;
		}

        if ($valid)
		{
            $user_data = $this->m_pro->get_row('user', array('user_uid' => $user_uid));

			if (empty($user_data))
			{
				$response = array('action' => 'print',
								  'message' => 'Something went wrong.');
				$response_code = REST_Controller::HTTP_NOT_FOUND;
				$valid = FALSE;
			}
		}

        if($valid)
        {
            $note = array(
                'user_note_uid' => generate_UID(),
                'user_id' => $user_data['id'],
                'title' => $title,
                'description' => $description,
                'create_on' => current_date_time(),
            );

            $insert_id = $this->m_pro->insert('post', $note);

            $response = array(
                'action' => 'alert',
                'message' => 'Note added successfully.',
            );

            $response_code = REST_Controller::HTTP_OK;
            $valid = FALSE;
        }
        $this->response($response, $response_code);
    }

    function note_delete() 
    {
        $note_uid = $this->delete('user_note_uid');
		$user_uid = $this->logged_in_user_uid;
		$valid = TRUE;

        if (empty($user_uid) OR empty($note_uid))
		{
			$response = array('action' => 'alert',
							  'message' => "Oops, Request parameters are missing. Please fill in all require fields.");
			$response_code = REST_Controller::HTTP_BAD_REQUEST;
			$valid = FALSE;
		}

        if ($valid)
		{
			$delete_id = $this->m_pro->delete_where('post', array('user_note_uid' => $note_uid));

			$response = array(
				'action' => 'alert_save',
				'message' => "User note deleted.",
			);
			$response_code = REST_Controller::HTTP_OK;
		}

		$this->response($response, $response_code);
	}

    function note_get() 
    {
		$user_uid = $this->logged_in_user_uid;
		$valid = TRUE;

        if (empty($user_uid))
		{
			$response = array('action' => 'alert',
							  'message' => "Oops, Request parameters are missing. Please fill in all require fields.");
			$response_code = REST_Controller::HTTP_BAD_REQUEST;
			$valid = FALSE;
		}

        if ($valid)
		{
            $user_data = $this->m_pro->get_row('user', array('user_uid' => $user_uid));

			if (empty($user_data))
			{
				$response = array('action' => 'print',
								  'message' => 'Something went wrong.');
				$response_code = REST_Controller::HTTP_NOT_FOUND;
				$valid = FALSE;
			}
		}

        if ($valid)
		{
			$posts = $this->m_pro->get('post', array('user_id' => $user_data['id']));

            $return_data = array();
            $return_data['posts'] = $posts;

			$response = array(
				'action' => 'save',
				'data_to_save' => $return_data,
			);
			$response_code = REST_Controller::HTTP_OK;
		}

		$this->response($response, $response_code);
	}

    function note_put() 
    {
        $data = $this->put('data');
		$valid = TRUE;

        if (empty($data['user_note_uid']))
		{
			$response = array('action' => 'alert',
							  'message' => "Oops, Request parameters are missing. Please fill in all require fields.");
			$response_code = REST_Controller::HTTP_BAD_REQUEST;
			$valid = FALSE;
		}

        if ($valid)
		{
			$delete_id = $this->m_pro->update('post', array('user_note_uid' => $data['user_note_uid']), $data);

			$response = array(
				'action' => 'alert_save',
				'message' => "User note updated.",
			);
			$response_code = REST_Controller::HTTP_OK;
		}

		$this->response($response, $response_code);
	}
}
