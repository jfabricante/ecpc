<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Classification extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('classification_model');
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of Classification Code',
				'content'  => 'classification/list_view',
				'entities' => $this->classification_model->browse()
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
				'title'    => $id ? 'Update Details' : 'Add Classification Code',
				'entity'   => $id ? $this->classification_model->read($config) : '',
			);

		$this->load->view('classification/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('id') ? $this->input->post('id') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		/*if ($this->vin_model->exist($config) && $id == 0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-error">Product Model has been duplicated!</div>');
		}
		else
		{*/
			$this->classification_model->store($config);

			if ($id > 0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Classification code has been updated!</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Classification code has been added!</div>');
			}
		//}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('classification/delete_view', $data);
	}

	public function delete()
	{
		$this->classification_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">Classification code has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_model_list()
	{
		echo json_encode($this->classification_model->browse(), true);
	}
}