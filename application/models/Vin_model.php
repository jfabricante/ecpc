<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vin_model extends CI_Model {

	private $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('VIN_MODEL')->result();
		}

		return $this->oracle->get('VIN_MODEL')->result_array();
	}

	public function browse_with_cp()
	{
		$fields = array(
				'a.ID',
				'a.PRODUCT_MODEL',
				'a.PRODUCT_YEAR',
				'a.DESCRIPTION',
				'a.LOT_SIZE',
				'a.CP_ID',
				'b.ENGINE_PREF',
				'b.SERIES',
				'b.PISTON_DISPLACEMENT',
				'b.BODY_TYPE',
				'b.YEAR_MODEL',
				'b.GROSS_WEIGHT',
				'b.CYLINDER',
				'b.FUEL',
				'b.STAMP'
			);

		$query = $this->oracle->select($fields)
				->from('VIN_MODEL a')
				->join('CP b', 'a.CP_ID = b.ID', 'LEFT')
				->order_by('a.PRODUCT_MODEL')
				->where('a.STATUS = 1')
				->get();

		return $query->result();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('VIN_MODEL', array('ID' => $params['id']));

			if ($params['type'] == 'object')
			{
				return $query->row();
			}
			
			return $query->row_array();
		}
	}

	public function store($params)
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		if ($id > 0)
		{
			$this->oracle->update('VIN_MODEL', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('VIN_MODEL', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('VIN_MODEL', array('ID' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->oracle->get_where('VIN_MODEL', array('PRODUCT_MODEL' => $params['product_model']));

		return $query->num_rows();
	}

	// Group model
	public function browseGroup(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('GROUP_MODEL')->result();
		}

		return $this->oracle->get('GROUP_MODEL')->result_array();
	}

	public function readGroupEntity($params)
	{
		$query = $this->oracle->get_where('GROUP_MODEL', array('ID' => $params));

		return $query->row();
	}

	public function storeGroup($params)
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		if ($id > 0)
		{
			$this->oracle->update('GROUP_MODEL', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('GROUP_MODEL', $params);
		}
	}

	public function deleteGroup()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('GROUP_MODEL', array('ID' => $id));

		return $this;
	}

	public function updateStatus($params)
	{
		$this->oracle->update('VIN_MODEL', $params, array('ID' => $params['ID']));
	}

	/*public function migrateItems()
	{
		$old_data = $this->browse(array('type' => 'array'));

		$this->oracle->insert_batch('VIN_MODEL', $old_data);

		return $old_data;
	}*/
}
