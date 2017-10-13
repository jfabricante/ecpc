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

		$config['LAST_USER']   = $this->session->userdata('fullname');
		$config['LAST_UPDATE'] = date('d-M-Y');

		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		$this->vin_control->store($config);

		if ($id > 0)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">Vin control has been updated!</div>');
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-success">Vin control has been added!</div>');
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

	public function ajax_vin_control_entity()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		$group = $this->vin_control->findGroupByProductModel($data);

		// Check the model if has a group
		if ($group)
		{
			$models = explode(",", $group['MODELS']);

			$recent = $this->vin_control->getLastEntryFromGroup($models);

			$current = $this->vin_control->readByProductModel($data);

			$config = array(
					'CODE'          => $current['CODE'],
					'VIN_NO'        => $recent['VIN_NO'],
					'LOT_NO'        => $current['LOT_NO'],
					'ENGINE'        => $current['ENGINE'],
					'PRODUCT_MODEL' => $current['PRODUCT_MODEL'],
					'MODEL_NAME'    => $current['MODEL_NAME'],
					'LAST_USER'     => $current['LAST_USER'],
					'LAST_UPDATE'   => $current['LAST_UPDATE'],
					'LAST_MODEL'    => $recent['PRODUCT_MODEL']
				);


			echo json_encode($config);
		}
		else
		{
			$current = $this->vin_control->readByProductModel($data);
			
			$config = array(
					'CODE'          => $current['CODE'],
					'VIN_NO'        => $current['VIN_NO'],
					'LOT_NO'        => $current['LOT_NO'],
					'ENGINE'        => $current['ENGINE'],
					'PRODUCT_MODEL' => $current['PRODUCT_MODEL'],
					'MODEL_NAME'    => $current['MODEL_NAME'],
					'LAST_USER'     => $current['LAST_USER'],
					'LAST_UPDATE'   => $current['LAST_UPDATE'],
					'LAST_MODEL'    => $current['PRODUCT_MODEL']
				);

			echo json_encode($config);
		}
	}

	/*public function run_migration()
	{
		echo '<pre>';
		print_r($this->vin_control->migrateItems());
		echo '</pre>'; die;
	}*/
}