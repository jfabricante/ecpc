<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vin_control_model extends CI_Model {

	private $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('VIN_CONTROL')->result();	
		}

		return $this->oracle->get('VIN_CONTROL')->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('VIN_CONTROL', array('ID' => $params['id']));

			if ($params['type'] == 'object')
			{
				return $query->row();
			}
			
			return $query->row_array();
		}
	}

	public function readByProductModel($params)
	{
		$query = $this->oracle->order_by('LOT_NO', 'DESC')->get_where('VIN_CONTROL', $params);

		return $query->row_array();
	}

	public function findGroupByProductModel($params)
	{
		if (isset($params['PRODUCT_MODEL']))
		{
			$query = $this->oracle->like('MODELS', $params['PRODUCT_MODEL'])
					->get('GROUP_MODEL');

			return $query->row_array();
		}
	}

	public function getLastEntryFromGroup($params)
	{
		$query = $this->oracle->order_by('ID', 'DESC')
				->where_in('PRODUCT_MODEL', $params)
				->get('VIN_CONTROL');

		return $query->row_array();
	}

	public function fetchLastLot($params)
	{
		$config = array();

		foreach ($params as $entity)
		{
			$query = $this->oracle->distinct('PRODUCT_MODEL')
					->order_by('ID', 'DESC')
					->where('PRODUCT_MODEL', $entity)
					->get('VIN_CONTROL');

			if ($query->num_rows())
			{
				array_push($config, $query->row_array());
			}
		}

		return count($config) ? $config : '';
	}

	public function store($params)
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		if ($id > 0)
		{
			$this->oracle->update('VIN_CONTROL', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('VIN_CONTROL', $params);
		}

		return $this;
	}

	public function delete()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('VIN_CONTROL', array('ID' => $id));

		return $this;
	}

	public function fetchLot($params)
	{
		$query = $this->oracle->select('LOT_NO')
				->from('VIN_CONTROL')
				->where($params)
				->get();

		return $query->result();
	}

	public function migrateItems()
	{
		$old_data = $this->browse(array('type' => 'array'));

		$old_data = array_map(function($item) {

			// Since the date format is enough and better solution 
			/*$str = sprintf("SELECT TO_DATE(TO_DATE('%s','YYYY-MM-DD HH24:MI:SS'),'DD-MON-YYYY HH24:MI:SS') AS D FROM DUAL", $item['LAST_UPDATE']);

			$date = $this->oracle->query($str);

			$item['LAST_UPDATE'] = $date->result_array()[0]['D'];*/

			$item['LAST_UPDATE'] = date('d-M-Y', strtotime($item['LAST_UPDATE']));

			return $item;
		}, $old_data);

		$this->oracle->insert_batch('VIN_CONTROL', $old_data);

		return $old_data;
	}
}
