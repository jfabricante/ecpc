<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Serial_model extends CI_Model {

	private $oracle;

	public function __construct() {
		parent::__construct();

		$this->oracle = $this->load->database('oracle', true);
	}

	public function browse(array $params = array('type' => 'object'))
	{
		if ($params['type'] == 'object')
		{
			return $this->oracle->get('SERIAL')->result();	
		}

		return $this->oracle->get('SERIAL')->result_array();
	}

	public function read(array $params = array('type' => 'object'))
	{
		if ($params['id'] > 0)
		{
			$query = $this->oracle->get_where('SERIAL', array('ID' => $params['id']));

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
			$this->oracle->update('SERIAL', $params, array('ID' => $id));
		}
		else
		{
			$this->oracle->insert('SERIAL', $params);
		}

		return $this;	
	}

	public function delete()
	{
		$id = $this->input->post('ID');

		$this->oracle->delete('SERIAL', array('ID' => $id));

		return $this;
	}

	public function exist($params)
	{
		$query = $this->oracle->get_where('SERIAL', array('product_model' => $params['product_model']));

		return $query->num_rows();
	}

	/*public function migrateItems()
	{
		$old_data = $this->browse(array('type' => 'array'));

		$this->oracle->insert_batch('SERIAL', $old_data);

		return $old_data;
	}*/
}
