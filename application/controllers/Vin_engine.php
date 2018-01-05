<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load third party
require_once APPPATH . '/third_party/PHPExcel/Classes/PHPExcel.php';

class Vin_engine extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library('session');

		$this->_redirect_unauthorized();

		$this->load->helper('form');

		$models = array('vin_model', 'portcode_model', 'classification_model', 'serial_model', 'vin_engine_model', 'vin_control_model', 'security_model');

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
		$config = array('INVOICE_NO' => $this->input->post('INVOICE_NO'));

		$entities = $this->vin_engine_model->fetchInvoiceItem($config);

		$entitiesArray = array_map(function($e) { return (array)$e; }, $entities);

		if ($this->input->post('ecpc'))
		{
			$this->_excel_report($entitiesArray, $this->input->post('INVOICE_NO'));
		}
		else
		{
			$this->_excelSummary($entitiesArray);
		}
		
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
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', 7200);

		$data       = json_decode(file_get_contents("php://input"), true);

		$new_vin    = array_column($data['items'], 'VIN_NO');
		$new_engine = array_column($data['items'], 'ENGINE_NO');

		// Vin and Engine no. from the resource
		$resources       = $this->vin_engine_model->fetchFields();
		$resource_vin    = array_column($resources, 'VIN_NO');
		$resource_engine = array_column($resources, 'ENGINE_NO');

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
			$current_date   = date('d-M-Y');
			$fullname       = $this->session->userdata('fullname');
			$vin_control    = $data['vin_control'];
			$last_item      = end($data['items']);
			$model          = $data['selected_model'];
			$items          = $data['items'];
			$portcode       = $data['PORTCODE'];
			$serial         = $data['SERIAL'];
			$classification = $data['CLASSIFICATION'];
			$entry_no       = $data['ENTRY_NO'];

			// Format for batch insertion
			for ($i = 0; $i < count($items); $i++)
			{
				$items[$i]['LAST_UPDATE']    = $current_date;
				$items[$i]['LAST_USER']      = $fullname;
				$items[$i]['PORTCODE']       = $portcode;
				$items[$i]['SERIAL']         = $serial;
				$items[$i]['CLASSIFICATION'] = $classification;
				$items[$i]['ENTRY_NO']       = $entry_no;
				$items[$i]['YEAR']           = date('Y');

			}

			// Perform batch insert
			$this->vin_engine_model->store_batch($items);

			// Format insert data for vin control
			$formatData = array(
					'CODE'          => $vin_control['CODE'],
					'VIN_NO'        => $vin_control['VIN_NO'],
					'LOT_NO'        => $last_item['LOT_NO'],
					'ENGINE'        => $vin_control['ENGINE'] ? $vin_control['ENGINE'] : '',
					'PRODUCT_MODEL' => $vin_control['PRODUCT_MODEL'],
					'MODEL_NAME'    => $vin_control['MODEL_NAME'],
					'LAST_USER'     => $fullname,
					'LAST_UPDATE'   => date('d-M-Y')
				);

			$this->vin_control_model->store($formatData);

			// Security
			if ($data['security'] != '')
			{
				$security = $data['security'];
				
				$security['LAST_USER']   = $this->session->userdata('fullname');
				$security['LAST_UPDATE'] = date('d-M-Y');

				$this->security_model->store($security);
			}

			echo 'Transaction has been completed!';
		}
	}

	public function store_cbu_resource()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 7200);

		$data       = json_decode(file_get_contents("php://input"), true);
		$new_vin    = array_column($data['items'], 'VIN_NO');
		$new_engine = array_column($data['items'], 'ENGINE_NO');

		// Vin and Engine no. from the RESOURCE
		$resources       = $this->vin_engine_model->fetchFields();
		$resource_vin    = array_column($resources, 'VIN_NO');
		$resource_engine = array_column($resources, 'ENGINE_NO');

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
			$portcode       = $data['PORTCODE'];
			$serial         = $data['SERIAL'];
			$classification = $data['CLASSIFICATION'];
			$entry_no       = $data['ENTRY_NO'];
			$current_date   = date('d-M-Y');
			$fullname       = $this->session->userdata('fullname');
			$items2         = $data['items2'];

			$config = array();

			// Format data for batch insertion
			for ($i = 0; $i < count($items); $i++)
			{
				$items[$i]['LAST_UPDATE']    = $current_date;
				$items[$i]['LAST_USER']      = $fullname;
				$items[$i]['SECURITY_NO']    = '';
				$items[$i]['PORTCODE']       = $portcode;
				$items[$i]['SERIAL']         = $serial;
				$items[$i]['CLASSIFICATION'] = $classification;
				$items[$i]['ENTRY_NO']       = $entry_no;
				$items[$i]['YEAR']           = date('Y');
			}

			// Perform batch insert
			$this->vin_engine_model->store_batch($items);

			// Format for excel content
			foreach ($items2 as $row)
			{
				$config[] = array(
						'PORTCODE'       => $portcode,
						'YEAR'           => date('Y'),
						'SERIAL'         => $serial,
						'ENTRY_NO'       => $entry_no,
						'MVDP'           => 'Y',
						'ENGINE_NO'      => $row['ENGINE_NO'],
						'CHASSIS_NO'     => $row['CHASSIS_NO'],
						'CLASSIFICATION' => $classification,
						'VIN_NO'         => $row['VIN_NO'],
						'MAKE'           => 'ISUZU',
						'SERIES'         => $row['SERIES'],
						'COLOR'          => $row['COLOR'],
						'PISTON'         => $row['PISTON_DISPLACEMENT'],
						'BODY_TYPE'      => $row['BODY_TYPE'],
						'MANUFACTURER'   => 'ISUZUPHILIPPINESCORPORATION',
						'YEAR_MODEL'     => $row['YEAR_MODEL'],
						'GROSS_WEIGHT'   => $row['GROSS_WEIGHT'],
						'NET_WEIGHT'     => $row['NET_WEIGHT'],
						'CYLINDER'       => $row['CYLINDER'],
						'FUEL'           => $row['FUEL']
					);
			}

			$this->_excel_report($config, $items[0]['INVOICE_NO']);
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

	protected function _excel_report($params, $invoice)
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

			// Number of row count
			$num_rows = $excelActiveSheet->getHighestRow();

			// Change the format from general to number format
			$excelActiveSheet->getStyle('A1:A' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('B1:B' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('C1:C' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('D1:D' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

    		$excelActiveSheet->getStyle('E1:E' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('F1:F' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('G1:G' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('H1:H' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('I1:I' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('J1:J' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('K1:K' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('L1:L' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('M1:M' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('N1:N' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('O1:O' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('P1:P' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('Q1:Q' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

    		$excelActiveSheet->getStyle('R1:R' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

    		$excelActiveSheet->getStyle('S1:S' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

    		$excelActiveSheet->getStyle('T1:T' . $num_rows)->getNumberFormat()
    				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


    		$items = 31;

    		// Setup items per page
    		for($i = 1; $i <= $num_rows; $i++)
    		{
    			// Page break
    			if (($i % $items) == 0)
    			{
    				if (floor($i / 31) == 1)
    				{
    					$excelActiveSheet->setBreak('A' . $items, PHPExcel_Worksheet::BREAK_ROW);
    				}
    				else
    				{
    					$excelActiveSheet->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
    				}

    				$items = $items + 30;
    			}
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

    		$sheetName = $params[0]['SERIAL'] . $params[0]['ENTRY_NO'] . $invoice;

    		// Set the footer details and pagination
    		$excelActiveSheet->getHeaderFooter()->setOddFooter('&L' . $sheetName . ' &D &T &RPage &P of &N');

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
			$filename = 'ecpc.xls';

			// Content header information
			header('Content-Type: application/vnd.ms-excel'); //mine type
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cached-Control: max-age=0');

			// Generate excel version using Excel 2017
			$objWriter = PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');

			$objWriter->save('php://output');

			$name = './resources/download/ecpc.xls';
			$objWriter->save($name);

			return $objWriter;
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">There was no data to process.</div>');
			redirect($this->agent->referrer());
		}

	}

	protected function _excel_report2($params)
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

			// Apply row height on the excel content
			$excelActiveSheet->getDefaultRowDimension()->setRowHeight(20);

			// Define the first row to auto-height
			$excelActiveSheet->getRowDimension('1')->setRowHeight(-1);

			// Number of row count
			$num_rows = $excelActiveSheet->getHighestRow();

			// Change the format from general to number format
			$excelActiveSheet->getStyle('A1:A' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('B1:B' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('C1:C' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('D1:D' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);

			$excelActiveSheet->getStyle('E1:E' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('F1:F' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('G1:G' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('H1:H' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('I1:I' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('J1:J' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('K1:K' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('L1:L' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('M1:M' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('N1:N' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('O1:O' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('P1:P' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('Q1:Q' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

			$excelActiveSheet->getStyle('R1:R' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

			$excelActiveSheet->getStyle('S1:S' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

			$excelActiveSheet->getStyle('T1:T' . $num_rows)->getNumberFormat()
					->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


			$items = 31;

			// Setup items per page
			for($i = 1; $i <= $num_rows; $i++)
			{
				// Page break
				if (($i % $items) == 0)
				{
					if (floor($i / 31) == 1)
					{
						$excelActiveSheet->setBreak('A' . $items, PHPExcel_Worksheet::BREAK_ROW);
					}
					else
					{
						$excelActiveSheet->setBreak('A' . $i, PHPExcel_Worksheet::BREAK_ROW);
					}

					$items = $items + 30;
				}
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

			$sheetName = $params[0]['SERIAL'] . $params[0]['ENTRY_NO'] . $invoice;

			// Set the footer details and pagination
			$excelActiveSheet->getHeaderFooter()->setOddFooter('&L' . $sheetName . ' &D &T &RPage &P of &N');

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
			$filename = 'ecpc.xls';

			// Content header information
			header('Content-Type: application/vnd.ms-excel'); //mine type
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cached-Control: max-age=0');

			// Generate excel version using Excel 2017
			$objWriter = PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');

			$objWriter->save('php://output');

			$name = './resources/download/ecpc.xls';
			$objWriter->save($name);

			return $objWriter;
		}
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">There was no data to process.</div>');
			redirect($this->agent->referrer());
		}

	}

	protected function _excelSummary($params)
	{
		if (count($params))
		{
			// Create an instance of PHPExcel
			$excelObj          = new PHPExcel();
			$excelActiveSheet  = $excelObj->getActiveSheet();
			$excelDefaultStyle = $excelObj->getDefaultStyle();

			$excelDefaultStyle->getAlignment()->setWrapText(true);
			$excelDefaultStyle->getFont()->setSize(12)->setName('Tahoma');

			// Add cell value
			$excelActiveSheet->mergeCells('A4:C4');
			$excelActiveSheet->setCellValue('A4','Engine and Chassis Report');


			$excelActiveSheet->mergeCells('A7:C7');
			$excelActiveSheet->mergeCells('E7:F7');
			$excelActiveSheet->mergeCells('I7:J7');
			$excelActiveSheet->setCellValue('A7','INVOICE NO.: ' . $params[0]['INVOICE_NO'])
							->setCellValue('E7','SERIES: ' . $params[0]['SERIES'])
							->setCellValue('I7','PORT: ' . $params[0]['PORTCODE']);

			$excelActiveSheet->mergeCells('A8:B8');
			$excelActiveSheet->mergeCells('E8:G8');
			$excelActiveSheet->setCellValue('A8','ENTRY NO.: ' . $params[0]['SERIAL'] . $params[0]['ENTRY_NO'])
							->setCellValue('E8','CLASS/TYPE: ' . $params[0]['BODY_TYPE']);

			$currentRowCell = 12;

			foreach ($params as $entity)
			{
				if ($entity['SEQUENCE'] == 1)
				{
					$excelActiveSheet->mergeCells('A' . $currentRowCell . ':C' . $currentRowCell);
					$excelActiveSheet->setCellValue('A' . $currentRowCell, 'YEAR MODEL: ' . $entity['YEAR_MODEL']);
					$currentRowCell++;

					$excelActiveSheet->mergeCells('A' . $currentRowCell . ':C' . $currentRowCell);
					$excelActiveSheet->setCellValue('A' . $currentRowCell, 'Model Name: ' . $entity['PRODUCT_MODEL']);
					$currentRowCell++;

					$excelActiveSheet->mergeCells('A' . $currentRowCell . ':C' . $currentRowCell);
					$excelActiveSheet->setCellValue('A' . $currentRowCell, 'Lot: ' . $entity['LOT_NO']);
					$currentRowCell+=2;

					$excelActiveSheet->mergeCells('C' . $currentRowCell . ':D' . $currentRowCell);
					$excelActiveSheet->setCellValue('C' . $currentRowCell, 'VIN Number');
					$excelActiveSheet->mergeCells('E' . $currentRowCell . ':F' . $currentRowCell);
					$excelActiveSheet->setCellValue('E' . $currentRowCell, 'Engine Number');
					$excelActiveSheet->setCellValue('G' . $currentRowCell, 'Security');

					$currentRowCell+=2;
				}

				$excelActiveSheet->setCellValue('B' . $currentRowCell, $entity['SEQUENCE']);
				$excelActiveSheet->mergeCells('C' . $currentRowCell . ':D' . $currentRowCell);
				$excelActiveSheet->setCellValue('C' . $currentRowCell, $entity['VIN_NO']);
				$excelActiveSheet->mergeCells('E' . $currentRowCell . ':F' . $currentRowCell);
				$excelActiveSheet->setCellValue('E' . $currentRowCell, $entity['ENGINE_NO']);
				$excelActiveSheet->setCellValue('G' . $currentRowCell, $entity['SECURITY_NO']);
				$currentRowCell++;

			}


			// Excel filename
			$filename = 'summary.xls';

			// Content header information
			header('Content-Type: application/vnd.ms-excel'); //mine type
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Cached-Control: max-age=0');

			// Generate excel version using Excel 2017
			$objWriter = PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');

			$objWriter->save('php://output');
		}
	}

	public function download()
	{
		$path = './resources/download/ecpc.xls';

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

	public function download_pdf()
	{
		$path = FCPATH . '/resources/download/report.pdf';

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

	public function download_masterlist()
	{
		$path = FCPATH . 'resources/download/masterlist.pdf';

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

	public function ajax_update_details()
	{
		ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 7200);

		$data = json_decode(file_get_contents("php://input"), true);
		
		if ($data['items'] && count($data['security']))
		{
			$this->vin_engine_model->update_batch($data['items']);

			// Security
			$security = $data['security'];
			
			$security['LAST_USER']   = $this->session->userdata('fullname');
			$security['LAST_UPDATE'] = date('d-M-Y');

			$this->security_model->store($security);			
		}
		else if ($data['items'])
		{
			$this->vin_engine_model->update_batch($data['items']);
		}


		$this->_create_pdf($data['items']);
		$this->_createMasterList($data['items']);
	}

	public function update_cbu_security()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		echo json_encode($this->vin_engine_model->update_batch($data['items']));
	}

	protected function _createMasterList($params)
	{
		// Load library
		$this->load->library('pdf');

		$lots = array_values(array_unique(array_column($params, 'LOT_NO')));

		// Create pdf instance
		$pdf = new PDF();

		$pdf->SetTitle('Vin Engine List');
		$pdf->SetSubject('Vin Engine List');
		$pdf->SetHeaderData('', 0, 'VIN ENGINE');

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
   
		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		    require_once(dirname(__FILE__).'/lang/eng.php');
		    $pdf->setLanguageArray($l);
		}

		$currentRow = 0;

		foreach ($lots as $lot)
		{
			// Set font
			$pdf->SetFont('helvetica', 'B', 16);

			// Add a page
			$pdf->AddPage();

			$pdf->Write(0, 'Model Name: ' . $params[0]['PRODUCT_MODEL'] . '	Lot No.: ' . $lot);
			$pdf->Ln(10);

			$pdf->SetFont('helvetica', '', 12);

			$content = '';

			for ($i = $currentRow; $i < $params ; $i++)
			{ 
				if ($lot == $params[$i]['LOT_NO'])
				{
					$content .= '<tr>
									<td border="1">' . $params[$i]['SEQUENCE'] . '</td>
									<td border="1">' . $params[$i]['VIN_NO'] . '</td>
									<td border="1">' . $params[$i]['ENGINE_NO'] . '</td>
								</tr>';
				}
				else
				{
					$currentRow = $i;
					break;
				}
			}

			$tbl = '<table cellspacing="0" style="text-align: center; font-weight: normal; padding: 4px 2px;">
					<thead>
						<tr >
							<th border="1" style="width: 50px;">No.</th>
							<th border="1">VIN</th>
							<th border="1">Engine</th>
						</tr>
					<thead>
					<tbody>
						' . $content . '
					</tbody>
				</table>';

			$pdf->writeHTML($tbl, true, false, false, false, '');
		}
		

		$name = FCPATH . '/resources/download/masterlist.pdf';
		$pdf->Output($name, 'F');

		$pdf->Output('masterlist.pdf', 'I');

	}

	protected function _create_pdf($params)
	{
		$this->load->library('pdf');

		$pdf = new PDF();

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		    require_once(dirname(__FILE__).'/lang/eng.php');
		    $pdf->setLanguageArray($l);
		}

		// set font
		$pdf->SetFont('helvetica', '', 25);

		// add a page
		$pdf->AddPage();

		// -----------------------------------------------------------------------------

		// $pdf->SetFont('helvetica', '', 10);

		// define barcode style
		$style = array(
		    'position'     => '',
		    'align'        => 'L',
		    'stretch'      => false,
		    'fitwidth'     => true,
		    'cellfitalign' => '',
		    'border'       => false,
		    'hpadding'     => 'auto',
		    'vpadding'     => 'auto',
		    'fgcolor'      => array(0,0,0),
		    'bgcolor'      => false,
		    'text'         => true,
		    'font'         => 'helvetica',  //array(255,255,255),
		    'stretchtext'  => 4
		);

		// set counter
		$counter = 1;

		foreach ($params as $entity)
		{
			$style['fontsize'] = 20;
			$pdf->Write(0, 'Model Name: ');
			$pdf->write1DBarcode($entity['PRODUCT_MODEL'], 'C39', '', '', '', 18, 0.4, $style, 'N');


			$pdf->Write(0, 'Lot Number: ' . $entity['DESCRIPTION'] . '  ' . $entity['LOT_NO']);

			$qr = $this->_hasChars($entity['QR'], ',');

			if (count($qr) > 1)
			{
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr[0], '130', '', 31, 31, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr[1], '', '', 31, 31, '', '', 'T', false, 300, 'R', false, false, 1, false, false, false);

			}
			else
			{
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr, '', '', 31, 31, '', '', 'T', false, 300, 'R', false, false, 1, false, false, false);
			}
			$pdf->Ln(15);

			$pdf->Write(0, 'Sequence Number: ' . $entity['SEQUENCE']);
			$pdf->Ln(20);

			$pdf->Write(0, 'Engine Number: ');
			$pdf->write1DBarcode($entity['ENGINE_NO'], 'C39', '', '', '', 18, 0.4, $style, 'N');

			$pdf->Write(0, 'Chassis Number: ');
			$pdf->write1DBarcode($entity['VIN_NO'], 'C39', '', '', '', 18, 0.4, $style, 'N');

			$style['fontsize'] = 0;
			$pdf->Write(0, 'Security Number: ');
			$pdf->write1DBarcode($entity['SECURITY_NO'], 'C39', '', '', '', 10, 0.4, $style, 'N');
			$pdf->Ln();
			$pdf->writeHTML("<br /><br />", true, false, false, false, '');

			$pdf->writeHTML("<hr />", true, false, false, false, '');

			$style['fontsize'] = 20;
			$pdf->Write(0, 'Model Name: ');
			$pdf->write1DBarcode($entity['PRODUCT_MODEL'], 'C39', '', '', '', 18, 0.4, $style, 'N');

			$pdf->Write(0, 'Lot Number: ' . $entity['DESCRIPTION'] . '  ' . $entity['LOT_NO']);
			if (count($qr) > 1)
			{
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr[0], '130', '', 31, 31, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr[1], '', '', 31, 31, '', '', '', false, 300, 'R', false, false, 1, false, false, false);
			}
			else
			{
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr, '', '', 31, 31, '', '', 'T', false, 300, 'R', false, false, 1, false, false, false);
			}
			$pdf->Ln(15);

			$pdf->Write(0, 'Sequence Number: ' . $entity['SEQUENCE']);
			$pdf->Ln(20);

			$pdf->Write(0, 'Engine Number: ');
			$pdf->write1DBarcode($entity['ENGINE_NO'], 'C39', '', '', '', 18, 0.4, $style, 'N');

			$pdf->Write(0, 'Chassis Number: ');
			$pdf->write1DBarcode($entity['VIN_NO'], 'C39', '', '', '', 18, 0.4, $style, 'N');

			$style['fontsize'] = 0;
			$pdf->Write(0, 'Security Number: ');
			$pdf->write1DBarcode($entity['SECURITY_NO'], 'C39', '', '', '', 10, 0.4, $style, 'N');

			if (count($params) == $counter)
			{
				break;
			}

			$pdf->AddPage();
			$counter++;
		}
		
		$name = FCPATH . '/resources/download/report.pdf';
		$pdf->Output($name, 'F');

		echo $pdf->Output('report.pdf', 'I');
	}

	// Return array else string
	protected function _hasChars($string, $needle)
	{
		if (strpos($string, $needle))
		{
			return explode($needle, $string);
		}

		return $string;
	}

	public function update_entity()
	{
		echo json_encode($this->vin_engine_model->update_entity($this->input->post()));
	}

	protected function _showVars($vars)
	{
		echo '<pre>';
		print_r($vars);
		echo '</pre>';
	}

	protected function _redirect_unauthorized()
	{
		if (count($this->session->userdata()) < 3)
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">Login first!</div>');

			redirect(base_url());
		}
	}

	public function search_field()
	{
		$entities = $this->vin_engine_model->searchField(trim($this->input->post('search_string')));

		$data = array(
				'title'    => $entities ? 'Result(s)' : 'Result not found',
				'content'  => 'vin_engine/search_field_view',
				'entities' => $entities
			);

		$this->load->view('include/template', $data);
	}

	}

	/*public function run_migration()
	{
		echo '<pre>';
		$this->vin_engine_model->migrateItems();
		echo '</pre>'; die;
	}*/

}