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

		// Load helper
		$this->load->helper('directory');

		$images = directory_map(FCPATH . '/resources/images/qr/');

		$config = array(
				'id'   => $id,
				'type' => 'object'
			);

		$data = array(
				'title'    => $id ? 'Update Details' : 'Add Vin Model',
				'entity'   => $id ? $this->vin_model->read($config) : '',
				'cp_items' => $this->cp_model->browse(),
				'images'   => $images
			);

		$this->load->view('vin/form_view', $data);
	}

	public function store()
	{
		$id = $this->input->post('ID') ? $this->input->post('ID') : 0;

		$config = array(
				'ID'            => $this->input->post('ID'),
				'PRODUCT_MODEL' => $this->input->post('PRODUCT_MODEL'),
				'PRODUCT_YEAR'  => $this->input->post('PRODUCT_YEAR'),
				'DESCRIPTION'   => $this->input->post('DESCRIPTION'),
				'LOT_SIZE'      => $this->input->post('LOT_SIZE'),
				'CP_ID'         => $this->input->post('CP_ID'),
				'QR'            => implode(',', $this->input->post('QR'))
			);

		// Trim the post data
		$config = array_map('trim', $config);

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

	public function handle_excel_report()
	{
		$data = $this->_fetchLatestDetails();

		if (count($data))
		{
			$this->_excelReport($data);
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">No Data!</div>');

			redirect($this->agent->referrer());
		}
	}

	protected function _excelReport($params)
	{
		if (count($params))
		{
			// Create an instance of PHPExcel
			$excelObj          = new PHPExcel();
			$excelActiveSheet  = $excelObj->getActiveSheet();
			$excelDefaultStyle = $excelObj->getDefaultStyle();

			$excelDefaultStyle->getFont()->setSize(10)->setName('Times New Roman');

			// Set the Active sheet
			$excelObj->setActiveSheetIndex(0);

			// Set alignment
			$excelDefaultStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			// Merge cell
			$excelActiveSheet->mergeCells('A1:B1');

			// Add header to the excel
			$excelActiveSheet->setCellValue('A1', 'MODEL')
							->setCellValue('C1', 'LOT NO')
							->setCellValue('D1', 'LAST CHASSIS NOS. ASSIGNED');

			$excelActiveSheet->getStyle('A1:D1')->getAlignment()->setWrapText(true); 

			//$excelActiveSheet->fromArray($params, NULL, 'A2');

			$currentRow = 2;

			$prevGroup = '';

			$prevRow = 0;

			foreach ($params as $row)
			{
				
				if ($prevGroup == $row['NAME'])
				{
					// Mark the occurence of same group
					if ($prevRow == 0)
					{
						$prevRow = $currentRow - 1;
					}
					
					$excelActiveSheet->setCellValue('B' . $currentRow, $row['MODEL']);
					$excelActiveSheet->setCellValue('C' . $currentRow, $row['LOT_NO']);	
				}
				else
				{
					if ($prevRow > 0)
					{
						$excelActiveSheet->mergeCells('A' . $prevRow . ':A' . ($currentRow - 1));
						$excelActiveSheet->mergeCells('D' . $prevRow . ':D' . ($currentRow - 1));

						$prevRow = 0;
					}

					$excelActiveSheet->setCellValue('A' . $currentRow, $row['NAME']);
					$excelActiveSheet->setCellValue('B' . $currentRow, $row['MODEL']);
					$excelActiveSheet->setCellValue('C' . $currentRow, $row['LOT_NO']);
					$excelActiveSheet->setCellValue('D' . $currentRow, $row['VIN_NO']);	
				}

				$prevGroup = $row['NAME'];
				
				$currentRow++;
			}

			// Paper Orientation
			$excelActiveSheet->getPageSetup()
				    ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			// Paper Size
			$excelActiveSheet->getPageSetup()
				->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

			// Set margins
			$excelActiveSheet->getPageMargins()->setTop(0.25);
			$excelActiveSheet->getPageMargins()->setRight(0.25);
			$excelActiveSheet->getPageMargins()->setLeft(0.25);
			$excelActiveSheet->getPageMargins()->setBottom(0.25);

			// Apply the column title on every pages
			$excelActiveSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1,1);

			// Fit the content to page
			$excelActiveSheet->getPageSetup()->setFitToWidth(1);    
			$excelActiveSheet->getPageSetup()->setFitToHeight(0);


			// Apply background color on cell
			$excelActiveSheet->getStyle('A1:D1')
							->getFill()
							->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
							->getStartColor()
							->setRGB('ffff99');

			// Set the alignment for the whole document
			$excelDefaultStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$excelDefaultStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

			$style = array(
				        'borders' => array(
				            'allborders' => array(
				                'style' => PHPExcel_Style_Border::BORDER_THIN,
				                'color' => array('rgb' => '000000')
				            )
				        ),
				        'alignment' => array(
				        	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
				        ),
				    );

			// Apply header style
			$excelActiveSheet->getStyle("A1:D1")->applyFromArray($style);

			// Excel filename
			$filename = 'group-model.xlsx';

			// Content header information
			header('Content-Type: application/vnd.ms-excel'); //mine type
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cached-Control: max-age=0');

			// Generate excel version using Excel 2017
			$objWriter = PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');

			$objWriter->save('php://output');
		}
	}

	protected function _fetchLatestDetails()
	{
		$entities = $this->vin_model->browseGroup();
			
		$config = array();

		foreach ($entities as $entity)
		{
			$models = explode(',', $entity->MODELS);

			$data = $this->vin_control_model->fetchLastLot($models);

			$lastVin = $this->vin_control_model->getLastEntryFromGroup($models);

			if (is_array($data))
			{
				foreach ($data as $row)
				{
					$config[] = array(
							'NAME'   => $entity->NAME,
							'MODEL'  => $row['PRODUCT_MODEL'],
							'LOT_NO' => $row['LOT_NO'],
							'VIN_NO' => $lastVin['VIN_NO']
						);
				}
			}
			
		}

		return $config;
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