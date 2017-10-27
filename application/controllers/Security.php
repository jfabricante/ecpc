<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Security extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');

		$this->_redirect_unauthorized();

		$this->load->model('security_model');
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of Security',
				'content'  => 'security/list_view',
				'entities' => $this->security_model->browse()
			);

		$this->load->view('include/template', $data);
	}

	public function form()
	{
		$id = $this->uri->segment(3) ? $this->uri->segment(3) : 0;

		$config = array(
				'id'   => $id,
				'type' => 'object'
			);

		$data = array(
				'title'    => $id ? 'Update Details' : 'Add Serial',
				'entity'   => $id ? $this->security_model->read($config) : '',
			);

		$this->load->view('security/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		$config['LAST_USER']   = $this->session->userdata('fullname');
		$config['LAST_UPDATE'] = date('d-M-Y');

		$this->security_model->store($config);

		if ($id > 0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">Security has been updated!</div>');
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">Security has been added!</div>');
		}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('security/delete_view', $data);
	}

	public function delete()
	{
		$this->security_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">Serial has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_serial_list()
	{
		echo json_encode($this->security_model->browse(), true);
	}

	public function get_last_number()
	{
		echo json_encode($this->security_model->fetchLastSecurity());
	}

	protected function _redirect_unauthorized()
	{
		if (count($this->session->userdata()) < 3)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Login first!</div>');

			redirect(base_url());
		}
	}

	/*public function migrate_data()
	{
		echo '<pre>';
		print_r($this->security_model->migrateItems());
		echo '</pre>'; die;
	}*/
}