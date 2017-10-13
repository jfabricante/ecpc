<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Serial extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('serial_model');
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of Serial',
				'content'  => 'serial/list_view',
				'entities' => $this->serial_model->browse()
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
				'entity'   => $id ? $this->serial_model->read($config) : '',
			);

		$this->load->view('serial/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		/*if ($this->vin_model->exist($config) && $id == 0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-error">Product Model has been duplicated!</div>');
		}
		else
		{*/
			$this->serial_model->store($config);

			if ($id > 0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Serial has been updated!</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Serial has been added!</div>');
			}
		//}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('serial/delete_view', $data);
	}

	public function delete()
	{
		$this->serial_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">Serial has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_serial_list()
	{
		echo json_encode($this->serial_model->browse(), true);
	}
}