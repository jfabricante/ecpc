<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load third party
require_once APPPATH . '/third_party/PHPExcel/Classes/PHPExcel.php';


class Vin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');

		$this->_redirect_unauthorized();

		// Arrays of models
		$models = array('vin_model', 'cp_model', 'vin_control_model');

		$this->load->model($models);
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
				'title'    => $id ? 'Update Details' : 'Add Vin Model',
				'entity'   => $id ? $this->vin_model->read($config) : '',
				'cp_items' => $this->cp_model->browse()
			);

		$this->load->view('vin/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		// Trim the post data
		$config = array_map('trim', $this->input->post());

		if ($this->vin_model->exist($config) && $id == 0)
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

	public function group_list()
	{
		$data = array(
				'title'    => 'List of Model Group',
				'content'  => 'vin/group_list_view',
				'entities' => $this->vin_model->browseGroup()
			);

		$this->load->view('include/template', $data);
	}

	public function group_form()
	{
		$id = $this->uri->segment(3) ? $this->uri->segment(3) : 0;

		$data = array(
				'title'   => $id ? 'Update Details' : 'Add Model Group',
				'content' => 'vin/group_form_view',
				'entity'  => $id ? $this->vin_model->readGroupEntity($id) : ''
			);

		$this->load->view('include/template', $data);
	}

	public function group_store()
	{
		$this->vin_model->storeGroup($this->input->post());

		redirect(base_url('index.php/vin/group_list'));
	}

	public function group_notice()
	{
		$data['id'] = $this->uri->segment(3);

		$this->load->view('vin/group_delete_view', $data);
	}

	public function group_delete()
	{
		$this->vin_model->deleteGroup();

		redirect(base_url('index.php/vin/group_list'));	
	}

	public function ajax_model_list()
	{
		echo json_encode($this->vin_model->browse_with_cp(), true);
	}

	public function ajax_model_list2()
	{
		echo json_encode($this->vin_model->browse(), true);
	}

	public function ajax_vin_lot()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		$lot = $this->vin_control_model->fetchLot($data);

		echo $lot ?  json_encode($lot, true) : '';
	}

	public function ajax_model_name()
	{
		echo json_encode(array_column($this->vin_model->browse(array('type' => 'array')), 'PRODUCT_MODEL'));
	}

	public function ajax_update_state()
	{
		$this->vin_model->updateStatus($this->input->post());
	}

	protected function _redirect_unauthorized()
	{
		if (count($this->session->userdata()) < 3)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Login first!</div>');

			redirect(base_url());
		}
	}

	/*public function run_migration()
	{
		echo '<pre>';
		print_r($this->vin_model->migrateItems());
		echo '</pre>'; die;
	}*/
}