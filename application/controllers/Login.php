<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$helpers = array('form');

		$this->load->helper($helpers);

		$this->load->library('session');
	}

	public function index()
	{
		$this->authenticate();
	}

	public function authenticate()
	{
		// Clear the session in case the user click the system unconsciously
		$this->session->sess_destroy();

		$user_data = $_SESSION['user_data'];

		// Create a new session variables
		$config = array(
				'employee_id' => $user_data['employee_id'],
				'employee_no' => $user_data['employee_no'],
				'nickname'    => $user_data['nickname'],
				'fullname'    => $user_data['full_name'],
				'fullname2'   => $user_data['full_name2'],
				'section'     => $user_data['section'],
				'department'  => $user_data['department'],
				'division'    => $user_data['division'],
				'user_access' => $user_data['user_access']
			);


		if (count($user_data) > 3)
		{
			$this->session->set_userdata($config);
			redirect('/vin/list_');
		}

		redirect('http://172.16.1.34/ipc_central');

	}

	public function dashboard()
	{
		$data = array(
			'title'   => 'Dashboard',
			'content' => 'dashboard_view',
		);

		$this->load->view('include/template', $data);
	}

	public function logout()
	{
		$this->session->sess_destroy();

		// Cast native session
		session_start();
		session_destroy();
		
		redirect('http://172.16.1.34/ipc_central');
	}

	protected function _validate_input()
	{
		$this->load->library('form_validation');

		$config = array(
		        array(
		                'field' => 'username',
		                'label' => 'Username',
		                'rules' => 'required|trim',
		                'errors' => array(
		                	'required' => 'You must provide a %s.',
		                ),
		        ),
		        array(
		                'field' => 'password',
		                'label' => 'Password',
		                'rules' => 'required|trim',
		                'errors' => array(
		                        'required' => 'You must provide a %s.',
		                ),
		        ),
			);

		$this->form_validation->set_rules($config);

		if ($this->form_validation->run() == false)
		{
			return false;
		}

		return true;
	}

	protected function _user_exist()
	{
		$this->load->model('user_model', 'user');

		return is_array($this->user->exist()) ? $this->user->exist() : false;
	}
}
