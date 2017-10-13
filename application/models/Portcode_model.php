<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portcode_model extends CI_Model {

	private $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('PORTCODE')->result();	
		}

		return $this->oracle->get('PORTCODE')->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('PORTCODE', array('ID' => $params['id']));

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
			$this->oracle->update('PORTCODE', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('PORTCODE', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('PORTCODE', array('ID' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->oracle->get_where('PORTCODE', array('PRODUCT_MODEL' => $params['product_model']));

		return $query->num_rows();
	}

	/*public function migrateItems()
	{
		$old_data = $this->browse(array('type' => 'array'));

		$this->oracle->insert_batch('PORTCODE', $old_data);

		return $old_data;
	}*/
}
