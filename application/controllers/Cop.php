<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cop extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');

		$this->_redirect_unauthorized();

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
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		$config['LAST_USER']        = $this->session->userdata('fullname');
		$config['LAST_UPDATE']      = date('d-M-Y');
		$config['CP_DATE']          = date('d-M-Y', strtotime($config['CP_DATE']));
		$config['ETD']              = date('d-M-Y', strtotime($config['ETD']));
		$config['ETA']              = date('d-M-Y', strtotime($config['ETA']));
		$config['PAYMENT_DATE']     = date('d-M-Y', strtotime($config['PAYMENT_DATE']));
		$config['TRANSMITTAL_DATE'] = date('d-M-Y', strtotime($config['TRANSMITTAL_DATE']));
		
		$this->cop_model->store($config);

		if ($id > 0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">CP item has been updated!</div>');
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">CP item has been added!</div>');
		}

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

	protected function _redirect_unauthorized()
	{
		if (count($this->session->userdata()) < 3)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Login first!</div>');

			redirect(base_url());
		}
	}
}