<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Color extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('color_model');
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of Color',
				'content'  => 'color/list_view',
				'entities' => $this->color_model->browse()
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
				'title'    => $id ? 'Update Details' : 'Add Color',
				'entity'   => $id ? $this->color_model->read($config) : '',
			);

		$this->load->view('color/form_view', $data);
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
			$this->color_model->store($config);

			if ($id > 0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Color has been updated!</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Color has been added!</div>');
			}
		//}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('color/delete_view', $data);
	}

	public function delete()
	{
		$this->color_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">Color has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_color_list()
	{
		echo json_encode($this->color_model->browse(), true);
	}
}