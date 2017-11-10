<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Security_model extends CI_Model {

	private $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('SECURITY')->result();	
		}

		return $this->oracle->get('SECURITY')->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('SECURITY', array('ID' => $params['id']));

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
			$this->oracle->update('SECURITY', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('SECURITY', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('SECURITY', array('ID' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->oracle->get_where('SECURITY', array('product_model' => $params['product_model']));

		return $query->num_rows();
	}

	public function fetchLastSecurity()
	{
		$query = $this->oracle->order_by('ID', 'DESC')
				->where('SECURITY_NO IS NOT NULL')
				->get('SECURITY');

		return $query->row();
	}

	/*public function migrateItems()
	{
		$old_data = $this->browse(array('type' => 'array'));

		$this->oracle->insert_batch('SECURITY', $old_data);

		return $old_data;
	}*/
}
