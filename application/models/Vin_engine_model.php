<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vin_engine_model extends CI_Model {

	public $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('VIN_ENGINE')->result();	
		}

		return $this->oracle->get('VIN_ENGINE')->result_array();
	}

	public function fetchFields()
	{
		$fields = array('VIN_NO', 'ENGINE_NO');

		$query = $this->oracle->select($fields)
				->from('VIN_ENGINE')
				->get();

		return $query->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('VIN_ENGINE', array('id' => $params['id']));

			if ($params['type'] == 'object')
			{
				return $query->row();
			}
			
			return $query->row_array();
		}
	}

	public function store($params)
	{
		$id = $this->input->post('id') ? $this->input->post('id') : 0;

		if ($id > 0)
		{
			$this->oracle->update('VIN_ENGINE', $params, array('id' => $id));
		}
		else
		{
			$this->oracle->insert('VIN_ENGINE', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('id');

		$this->oracle->delete('VIN_MODEL', array('id' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->oracle->get_where('VIN_MODEL', array('product_model' => $params['product_model']));

		return $query->num_rows();
	}

	public function fetchInvoice()
	{
		$query = $this->oracle->select('INVOICE_NO')
				->from('VIN_ENGINE')
				->distinct('INVOICE_NO')
				->get();

		return $query->result();
	}

	public function fetchInvoiceItem($params)
	{
		$fields = array(
					'a.ID',
					'a.SEQUENCE',
					'a.PORTCODE',
					'a.SERIAL',
					'a.ENTRY_NO',
					'a.VIN_NO',
					'a.ENGINE_NO',
					'a.CLASSIFICATION',
					'a.LOT_NO',
					'a.INVOICE_NO',
					'a.YEAR',
					'a.COLOR',
					'a.SECURITY_NO',
					'a.PRODUCT_MODEL',
					'c.SERIES',
					'c.PISTON_DISPLACEMENT',
					'c.BODY_TYPE',
					'c.YEAR_MODEL',
					'c.GROSS_WEIGHT',
					'c.NET_WEIGHT',
					'c.CYLINDER',
					'c.FUEL',
				);

		// If the first query has no result
		$query = $this->oracle->select($fields)
				->from('VIN_ENGINE a')
				->join('VIN_MODEL b', 'a.PRODUCT_MODEL = b.PRODUCT_MODEL', 'INNER')
				->join('CP c', 'b.CP_ID = c.ID', 'LEFT')
				->where($params)
				->order_by('a.ID')
				->get();

		// Generate query that directs to cp details model
		if (!$query->num_rows())
		{
			$query = $this->oracle->select($fields)
					->from('VIN_ENGINE a')
					->join('CP c', 'a.PRODUCT_MODEL = c.MODEL', 'INNER')
					->where($params)
					->order_by('a.ID')
					->get();
		}

		return $query->result();
	}

	public function fetchInvoiceView($params)
	{
		$query = $this->oracle->order_by('ID')->get_where('VIN_ENGINE', $params);
	
		return $query->result();
	}

	public function fetchModelItems($params)
	{
		$fields = array(
				'a.*',
				'b.DESCRIPTION',
				'b.QR'
			);

		$clause = sprintf('LOT_NO BETWEEN %s AND %s', $params['lot_from'], $params['lot_to']);

		$query = $this->oracle->select($fields)
				->from('VIN_ENGINE a')
				->join('VIN_MODEL b', 'a.PRODUCT_MODEL = b.PRODUCT_MODEL', 'LEFT')
				->where('a.PRODUCT_MODEL', $params['product_model'])
				->where($clause)
				->order_by('a.ID')
				->get();

		return $query->result();
	}

	public function store_batch(array $data)
	{
		$this->oracle->insert_batch('VIN_ENGINE', $data);
	}

	public function update_batch(array $data)
	{
		foreach ($data as $entity) 
		{
			$config = array(
					'VIN_NO'      => $entity['VIN_NO'],
					'ENGINE_NO'   => $entity['ENGINE_NO'],
					'SECURITY_NO' => $entity['SECURITY_NO'],
				);

			$this->oracle->update('VIN_ENGINE', $config, array('ID' => $entity['ID']));
		}
	}

	public function update_entity($params)
	{
		$this->oracle->update('VIN_ENGINE', $params, array('ID' => $params['ID']));

		return $this->oracle->last_query();
	}	

	public function fetchDistinctModel()
	{
		$query = $this->oracle->distinct('PRODUCT_MODEL')
				->select('a.PRODUCT_MODEL')
				->from('VIN_ENGINE a')
				->join('VIN_MODEL b', 'a.PRODUCT_MODEL = b.PRODUCT_MODEL', 'INNER')
				->where('b.STATUS = 1')
				->get();

		return $query->result();
	}

	public function fetchDistinctLot($params)
	{
		$query = $this->oracle->distinct('LOT_NO')
				->select('LOT_NO')
				->order_by('LOT_NO', 'ASC')
				->where($params)
				->get('VIN_ENGINE');

		return $query->result();
	}

	public function searchField($params)
	{
		if ($params)
		{
			$fields = array(
					'a.ID',
					'a.SEQUENCE',
					'a.PRODUCT_MODEL',
					'a.VIN_NO',
					'a.ENGINE_NO',
					'a.LOT_NO',
					'a.INVOICE_NO',
					'a.SECURITY_NO',
					'a.PORTCODE',
					'a.SERIAL',
					'a.CLASSIFICATION',
					'a.ENTRY_NO',
					'a.COLOR',
					'a.KEY_NO',
					'b.CP_NO',
					'c.SERIES',
					'c.PISTON_DISPLACEMENT',
					'c.BODY_TYPE',
					'c.YEAR_MODEL',
					'c.GROSS_WEIGHT',
					'c.CYLINDER',
					'c.YEAR_MODEL'
				);
			$query = $this->oracle->select($fields)
					->from('VIN_ENGINE a')
					->join('COP b', 'a.INVOICE_NO = b.INVOICE_NO', 'LEFT')
					->join('CP c', 'a.PRODUCT_MODEL = c.MODEL', 'LEFT')
					->like('a.VIN_NO', $params)
					->or_like('a.ENGINE_NO', $params)
					->or_like('a.INVOICE_NO', $params)
					->get();

			return $query->result();
		}

		return false;
	}


	public function migrateItems()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 7200);

		$old_data = $this->browse(array('type' => 'array'));

		for ($i = 0; $i < count($old_data); $i++)
		{
			$old_data[$i]['LAST_UPDATE'] = $old_data[$i]['LAST_UPDATE'] ? date('d-M-Y', strtotime($old_data[$i]['LAST_UPDATE'])) : '';
			$old_data[$i]['PROCESS_DATE'] = $old_data[$i]['PROCESS_DATE'] ? date('d-M-Y', strtotime($old_data[$i]['PROCESS_DATE'])) : '';
		}

		$this->oracle->insert_batch('VIN_ENGINE', $old_data);

		return $old_data;
	}
}
