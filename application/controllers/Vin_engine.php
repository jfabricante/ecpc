<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load file
require_once APPPATH . '/third_party/PHPExcel/Classes/PHPExcel.php';

class Vin_engine extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('form');
		$this->load->model('vin_model');
	}

	public function index()
	{
		$data = array(
				'title'    => 'Engine and Chassis Form',
				'content'  => 'vin_engine/index_view',
				'entities' => $this->vin_model->browse()
			);

		$this->load->view('include/template', $data);
	}

	public function list_()
	{
		$data = array(
				'title'    => 'List of Vin Model',
				'content'  => 'vin/list_view',
				'entities' => $this->vin_model->browse()
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
				'title'   => $id ? 'Update Details' : 'Add Vin Model',
				'entity'  => $id ? $this->vin_model->read($config) : ''
			);

		$this->load->view('vin/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('id') ? $this->input->post('id') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		if ($this->vin_model->exist($config))
		{
			$this->session->set_flashdata('message', '<div class="alert alert-error">Product Model has been duplicated!</div>');
		}
		else
		{
			$this->vin_model->store($config);

			if ($id > 0)
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Vin model has been updated!</div>');
			}
			else
			{
				$this->session->set_flashdata('message', '<div class="alert alert-success">Vin model has been added!</div>');
			}
		}

		redirect($this->agent->referrer());
	}

	public function notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('vin/delete_view', $data);
	}

	public function delete()
	{
		$this->vin_model->delete();

		$this->session->set_flashdata('message', '<div class="alert alert-success">Vin model has been deleted!</div>');

		redirect($this->agent->referrer());
	}

	public function analyzer()
	{
		$excelObj = new PHPExcel();

		echo '<pre>';
		print_r($excelObj);
		echo '</pre>'; die;
	}
}