<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cp extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('cp_model');
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of CP Details',
				'content'  => 'cp/list_view',
				'entities' => $this->cp_model->browse()
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
				'title'   => $id ? 'Update Details' : 'Add CP',
				'entity'  => $id ? $this->cp_model->read($config) : ''
			);

		$this->load->view('cp/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('id') ? $this->input->post('id') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		/*echo '<pre>';
		print_r($config);
		echo '</pre>'; die;*/

		/*if ($this->vin_model->exist($config))
		{
			$this->session->set_flashdata('message', '<div class="alert alert-error">Product Model has been duplicated!</div>');
		}
		else
		{*/
			$this->cp_model->store($config);

			if ($id > 0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">CP details has been updated!</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">CP details has been added!</div>');
			}
		//}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('cp/delete_view', $data);
	}

	public function delete()
	{
		$this->cp_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">CP has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_cp_entity()
	{
		$data = json_decode(file_get_contents("php://input"), true);
		$data['type'] = 'object';

		echo json_encode($this->cp_model->read($data), true);
	}
}