<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load file
require_once APPPATH . '/third_party/PHPExcel/Classes/PHPExcel.php';

class Vin_engine extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('form');

		$models = array('vin_model', 'portcode_model', 'classification_model', 'serial_model', 'vin_engine_model', 'vin_control_model');

		$this->load->model($models);
	}

	public function ckd()
	{
		$data = array(
				'title'   => 'Engine and Chassis Form CKD',
				'content' => 'vin_engine/ckd_view',
			);

		$this->load->view('include/template', $data);
	}

	public function cbu()
	{
		$data = array(
				'title'   => 'Engine and Chassis Form CBU',
				'content' => 'vin_engine/cbu_view',
			);

		$this->load->view('include/template', $data);
	}

	public function invoice()
	{
		$data = array(
				'title'    => '',
				'content'  => 'vin_engine/invoice_view',
			);

		$this->load->view('include/template', $data);
	}

	public function invoice_process()
	{
		$entities = $this->vin_engine_model->fetchInvoiceItem($this->input->post());
		
		$config = array();

		foreach ($entities as $entity)
		{
			$config[] = array(
					'portcode'       => $entity->portcode,
					'year'           => $entity->year,
					'serial'         => $entity->serial,
					'entry_no'       => $entity->entry_no,
					'mvdp'           => 'Y',
					'engine_no'      => $entity->engine_no,
					'chassis_no'     => $entity->vin_no,
					'classification' => $entity->classification,
					'vin_no'         => $entity->vin_no,
					'make'           => 'ISUZU',
					'series'         => $entity->series,
					'color'          => $entity->color,
					'piston'         => strtoupper($entity->piston_displacement),
					'body_type'      => $entity->body_type,
					'manufacturer'   => 'ISUZUPHILIPPINESCORPORATION',
					'year_model'     => $entity->year_model,
					'gross_weight'   => number_format($entity->gross_weight, 2),
					'net_weight'     => $entity->net_weight,
					'cylinder'       => $entity->cylinder,
					'fuel'           => strtoupper($entity->fuel)
				);
		}

		$this->_excel_report($config);
	}

	public function model_view()
	{
		$data = array(
				'title'   => 'Select Model Name and Lot No.',
				'content' => '/vin_engine/model_view'
			);

		$this->load->view('include/template', $data);
	}

	public function store_ckd_resource()
	{
		$data       = json_decode(file_get_contents("php://input"), true);
		$new_vin    = array_column($data['items'], 'vin_no');
		$new_engine = array_column($data['items'], 'engine_no');

		// Vin and Engine no. from the resource
		$resources       = $this->vin_engine_model->fetchFields();
		$resource_vin    = array_column($resources, 'vin_no');
		$resource_engine = array_column($resources, 'engine_no');

		// Vin and Engine no. that matches
		$exist_vin    = array_intersect($resource_vin, $new_vin);
		$exist_engine = array_intersect($resource_engine, $new_engine);

		if (count($exist_vin))
		{
			echo json_encode($exist_vin, true);
		}
		else if (count($exist_engine))
		{
			echo json_encode($exist_engine, true);
		}
		else
		{
			$current_date   = date('Y-m-d H:i:s');
			$fullname       = $this->session->userdata('fullname');
			$vin_control    = $data['vin_control'];
			$last_item      = end($data['items']);
			$model          = $data['selected_model'];
			$items          = $data['items'];
			$portcode       = $data['portcode'];
			$serial         = $data['serial'];
			$classification = $data['classification'];
			$entry_no       = $data['entry_no'];

			$config = array();

			// Format for batch insertion
			for ($i = 0; $i < count($items); $i++)
			{
				$items[$i]['last_update']    = $current_date;
				$items[$i]['last_user']      = $fullname;
				$items[$i]['security_no']    = '';
				$items[$i]['portcode']       = $portcode;
				$items[$i]['serial']         = $serial;
				$items[$i]['classification'] = $classification;
				$items[$i]['entry_no']       = $entry_no;
				$items[$i]['year']           = date('Y');

				$config[] = array(
						'portcode'       => $portcode,
						'year'           => date('Y'),
						'serial'         => $serial,
						'entry_no'       => $entry_no,
						'mvdp'           => 'Y',
						'engine_no'      => $items[$i]['engine_no'],
						'chassis_no'     => $items[$i]['vin_no'],
						'classification' => str_pad($classification, 3, '0', STR_PAD_LEFT),
						'vin_no'         => $items[$i]['vin_no'],
						'make'           => 'ISUZU',
						'series'         => $model['series'],
						'color'          => $items[$i]['color'],
						'piston'         => strtoupper($model['piston_displacement']),
						'body_type'      => $model['body_type'],
						'manufacturer'   => 'ISUZUPHILIPPINESCORPORATION',
						'year_model'     => $model['year_model'],
						'gross_weight'   => number_format($model['gross_weight'], 2),
						'net_weight'     => '',
						'cylinder'       => $model['cylinder'],
						'fuel'           => strtoupper($model['fuel'])
					);
			}

			// Perform batch insert
			$this->vin_engine_model->store_batch($items);

			// Create excel file
			$this->_excel_report($config);
			
			// Format insert data for vin control
			$config = array(
					'code'          => $vin_control['code'],
					'vin_no'        => $vin_control['vin_no'],
					'lot_no'        => $last_item['lot_no'],
					'engine'        => $vin_control['engine'],
					'product_model' => $vin_control['product_model'],
					'model_name'    => $vin_control['model_name'],
					'last_user'     => $fullname,
					'last_update'   => $vin_control['last_update']
				);	

			$this->vin_control_model->store($config);
		}
	}

	public function store_cbu_resource()
	{
		$data       = json_decode(file_get_contents("php://input"), true);
		$new_vin    = array_column($data['items'], 'vin_no');
		$new_engine = array_column($data['items'], 'engine_no');

		// Vin and Engine no. from the resource
		$resources       = $this->vin_engine_model->fetchFields();
		$resource_vin    = array_column($resources, 'vin_no');
		$resource_engine = array_column($resources, 'engine_no');

		// Vin and Engine no. that matches
		$exist_vin    = array_intersect($resource_vin, $new_vin);
		$exist_engine = array_intersect($resource_engine, $new_engine);

		if (count($exist_vin))
		{
			echo json_encode($exist_vin, true);
		}
		else if (count($exist_engine))
		{
			echo json_encode($exist_engine, true);
		}
		else
		{
			$items          = $data['items'];
			$portcode       = $data['portcode'];
			$serial         = $data['serial'];
			$classification = $data['classification'];
			$entry_no       = $data['entry_no'];
			$current_date   = date('Y-m-d H:i:s');
			$fullname       = $this->session->userdata('fullname');
			$items2         = $data['items2'];

			$config = array();

			// Format data for batch insertion
			for ($i = 0; $i < count($items); $i++)
			{
				$items[$i]['last_update']    = $current_date;
				$items[$i]['last_user']      = $fullname;
				$items[$i]['security_no']    = '';
				$items[$i]['portcode']       = $portcode;
				$items[$i]['serial']         = $serial;
				$items[$i]['classification'] = $classification;
				$items[$i]['entry_no']       = $entry_no;
				$items[$i]['year']           = date('Y');
			}

			// Perform batch insert
			$this->vin_engine_model->store_batch($items);

			// Format for excel content
			foreach ($items2 as $row)
			{
				$config[] = array(
						'portcode'       => $portcode,
						'year'           => date('Y'),
						'serial'         => $serial,
						'entry_no'       => $entry_no,
						'mvdp'           => 'Y',
						'engine_no'      => $row['engine_no'],
						'chassis_no'     => $row['chassis_no'],
						'classification' => $classification,
						'vin_no'         => $row['vin_no'],
						'make'           => 'ISUZU',
						'series'         => $row['series'],
						'color'          => $row['color'],
						'piston'         => $row['piston_displacement'],
						'body_type'      => $row['body_type'],
						'manufacturer'   => 'ISUZUPHILIPPINESCORPORATION',
						'year_model'     => $row['year_model'],
						'gross_weight'   => $row['gross_weight'],
						'net_weight'     => $row['net_weight'],
						'cylinder'       => $row['cylinder'],
						'fuel'           => $row['fuel']
					);
			}

			$this->_excel_report($config);
		}
	}

	protected function _handle_upload()
	{
		$config = array(
				//'upload_path'   => './resources/thumbnail',
				'allowed_types' => 'xlsx|xls|csv',
			);

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('file-upload'))
		{
			$error = array('error' => $this->upload->display_errors());

			return $this->upload->display_errors();
		}

		return $this->upload;
	}

	protected function _excel_report($params)
	{
		// Create an instance of PHPExcel
		$excelObj          = new PHPExcel();
		$excelActiveSheet  = $excelObj->getActiveSheet();
		$excelDefaultStyle = $excelObj->getDefaultStyle();

		$excelDefaultStyle->getFont()->setSize(10)->setName('Times New Roman');

		// Set the Active sheet
		$excelObj->setActiveSheetIndex(0);

		// Add header to the excel
		$excelActiveSheet->setCellValue('A1', 'Port Code')
						->setCellValue('B1', 'Year')
						->setCellValue('C1', 'Entry Serial')
						->setCellValue('D1', 'Entry No.')
						->setCellValue('E1', 'MVDP Member')
						->setCellValue('F1', 'Engine Number')
						->setCellValue('G1', 'Chassis Number')
						->setCellValue('H1', 'Classification Code')
						->setCellValue('I1', 'VIN No.')
						->setCellValue('J1', 'Vehicle Make')
						->setCellValue('K1', 'Series')
						->setCellValue('L1', 'Color')
						->setCellValue('M1', 'Piston Displacement')
						->setCellValue('N1', 'Body Type')
						->setCellValue('O1', 'Manufacturer')
						->setCellValue('P1', 'Year Model')
						->setCellValue('Q1', 'Gross Wt')
						->setCellValue('R1', 'Net Wt')
						->setCellValue('S1', 'No Cylinder')
						->setCellValue('T1', 'Fuel');

		$excelActiveSheet->getStyle('A1:T1')->getAlignment()->setWrapText(true); 

		$excelActiveSheet->fromArray($params, NULL, 'A2');

		// Apply background color on cell
		$excelActiveSheet->getStyle('A1:T1')
						->getFill()
						->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
						->getStartColor()
						->setRGB('ffff99');

		// Set the alignment for the whole document
		$excelDefaultStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
		$excelActiveSheet->getStyle("A1:T1")->applyFromArray($style);

		// Excel filename
		$filename = 'ecpc.xlsx';

		// Content header information
		header('Content-Type: application/vnd.ms-excel'); //mine type
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cached-Control: max-age=0');

		// Generate excel version using Excel 2017
		$objWriter = PHPExcel_IOFactory::createWriter($excelObj, 'Excel2007');

		$objWriter->save('php://output');

		$name = './resources/download/ecpc.xlsx';
		$objWriter->save($name);

		return $objWriter;
	}

	public function download()
	{
		$path = './resources/download/ecpc.xlsx';

		if (file_exists($path))
		{
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($path));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($path));
			ob_clean();
			flush();
			readfile($path);
		}
	}

	public function ajax_fetch_invoice_list()
	{
		echo json_encode($this->vin_engine_model->fetchInvoice());
	}

	public function ajax_fetch_invoice_items()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		echo json_encode($this->vin_engine_model->fetchInvoiceView($data), true);
	}

	public function ajax_fetch_model_items()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		echo json_encode($this->vin_engine_model->fetchModelItems($data), true);
	}

	public function ajax_distinct_model()
	{
		echo json_encode($this->vin_engine_model->fetchDistinctModel());
	}

	public function ajax_distinct_lot()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		echo json_encode($this->vin_engine_model->fetchDistinctLot($data), true);
	}

}