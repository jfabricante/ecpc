<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portcode extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->model('portcode_model');
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of Portcode',
				'content'  => 'portcode/list_view',
				'entities' => $this->portcode_model->browse()
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
				'title'    => $id ? 'Update Details' : 'Add Vin Model',
				'entity'   => $id ? $this->portcode_model->read($config) : '',
			);

		$this->load->view('portcode/form_view', $data);
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
			$this->portcode_model->store($config);

			if ($id > 0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Port code has been updated!</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Port code has been added!</div>');
			}
		//}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('portcode/delete_view', $data);
	}

	public function delete()
	{
		$this->portcode_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">Port code has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function ajax_portcode_list()
	{
		echo json_encode($this->portcode_model->browse(), true);
	}

	/*public function run_migration()
	{
		echo '<pre>';
		print_r($this->portcode_model->migrateItems());
		echo '</pre>'; die;
	}*/
}