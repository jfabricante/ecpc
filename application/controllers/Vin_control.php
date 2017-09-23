<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vin_control extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('vin_control_model', 'vin_control');
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of Vin Control',
				'content'  => 'vin_control/list_view',
				'entities' => $this->vin_control->browse()
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
				'title'   => $id ? 'Update Details' : 'Add Vin Control',
				'entity'  => $id ? $this->vin_control->read($config) : ''
			);

		$this->load->view('vin_control/form_view', $data);
	}

	public function store()
	{
		$config = array_map('trim', $this->input->post());

		$config['last_user']   = $this->session->userdata('fullname');
		$config['last_update'] = date('Y-m-d H:i:s');

		$id = $this->input->post('id') ? $this->input->post('id') : 0;

		$this->vin_control->store($config);

		if ($id > 0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">Vin model has been updated!</div>');
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">Vin model has been added!</div>');
		}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('vin_control/delete_view', $data);
	}

	public function delete()
	{
		$this->vin_control->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">Vin model has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_category_list()
	{
		echo json_encode($this->vin_control->browse());
	}
}