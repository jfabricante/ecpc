<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cop extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$models = array('cop_model', 'vin_engine_model');

		$this->load->model($models);
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of CP',
				'content'  => 'cop/list_view',
				'entities' => $this->cop_model->browse()
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
				'title'    => $id ? 'Update Details' : 'Add CP Entry',
				'entity'   => $id ? $this->cop_model->read($config) : '',
				'invoices' => $this->vin_engine_model->fetchInvoice()
			);

		$this->load->view('cop/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('id') ? $this->input->post('id') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		$config['last_user']   = $this->session->userdata('fullname');
		$config['last_update'] = date('Y-m-d H:i:s');

		/*if ($this->vin_model->exist($config) && $id == 0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-error">Product Model has been duplicated!</div>');
		}
		else
		{*/
			$this->cop_model->store($config);

			if ($id > 0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">CP item has been updated!</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">CP item has been added!</div>');
			}
		//}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('cop/delete_view', $data);
	}

	public function delete()
	{
		$this->cop_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">CP item has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_cop_list()
	{
		echo json_encode($this->cop_model->browse(), true);
	}
}