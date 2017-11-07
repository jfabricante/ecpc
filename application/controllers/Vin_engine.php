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
		
		$config = array();

		foreach ($entities as $entity)
		{
			$config[] = array(
					'PORTCODE'       => $entity->PORTCODE,
					'YEAR'           => $entity->YEAR,
					'SERIAL'         => $entity->SERIAL,
					'ENTRY_NO'       => $entity->ENTRY_NO,
					'MVDP'           => 'Y',
					'ENGINE_NO'      => $entity->ENGINE_NO,
					'CHASSIS_NO'     => $entity->VIN_NO,
					'CLASSIFICATION' => $entity->CLASSIFICATION,
					'VIN_NO'         => $entity->VIN_NO,
					'MAKE'           => 'ISUZU',
					'SERIES'         => $entity->SERIES,
					'COLOR'          => $entity->COLOR,
					'PISTON'         => strtoupper($entity->PISTON_DISPLACEMENT),
					'BODY_TYPE'      => $entity->BODY_TYPE,
					'MANUFACTURER'   => 'ISUZUPHILIPPINESCORPORATION',
					'YEAR_MODEL'     => $entity->YEAR_MODEL,
					'GROSS_WEIGHT'   => number_format($entity->GROSS_WEIGHT, 2),
					'NET_WEIGHT'     => $entity->NET_WEIGHT,
					'CYLINDER'       => $entity->CYLINDER,
					'FUEL'           => strtoupper($entity->FUEL)
				);
		}

		$this->_excel_report($config, $this->input->post('INVOICE_NO'));
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

			$config = array();

			// Format for batch insertion
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

				$config[] = array(
						'PORTCODE'       => $portcode,
						'YEAR'           => date('Y'),
						'SERIAL'         => $serial,
						'ENTRY_NO'       => $entry_no,
						'MVDP'           => 'Y',
						'ENGINE_NO'      => $items[$i]['ENGINE_NO'],
						'CHASSIS_NO'     => $items[$i]['VIN_NO'],
						'CLASSIFICATION' => str_pad($classification, 3, '0', STR_PAD_LEFT),
						'VIN_NO'         => $items[$i]['VIN_NO'],
						'MAKE'           => 'ISUZU',
						'SERIES'         => $model['SERIES'],
						'COLOR'          => $items[$i]['COLOR'],
						'PISTON'         => strtoupper($model['PISTON_DISPLACEMENT']),
						'BODY_TYPE'      => $model['BODY_TYPE'],
						'MANUFACTURER'   => 'ISUZUPHILIPPINESCORPORATION',
						'YEAR_MODEL'     => $model['YEAR_MODEL'],
						'GROSS_WEIGHT'   => number_format($model['GROSS_WEIGHT'], 2),
						'NET_WEIGHT'     => '',
						'CYLINDER'       => $model['CYLINDER'],
						'FUEL'           => strtoupper($model['FUEL'])
					);
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

			// Create excel file
			$this->_excel_report($config, $items[0]['INVOICE_NO']);
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
		else
		{
			$this->session->set_flashdata('message', '<div class="alert alert-warning">There was no data to process.</div>');
			redirect($this->agent->referrer());
		}

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

		$this->_createMasterList($data['items']);
		$this->_create_pdf($data['items']);
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

		// Set font
		$pdf->SetFont('helvetica', 'B', 16);

		// Add a page
		$pdf->AddPage();

		$pdf->Write(0, 'Model Name: ' . $params[0]['PRODUCT_MODEL'] . '	Lot No.: ' . $params[0]['LOT_NO']);
		$pdf->Ln(10);

		$pdf->SetFont('helvetica', '', 12);

		$content = '';

		foreach($params as $entity)
		{
			$content .= '<tr>
							<td border="1">' . $entity['SEQUENCE'] . '</td>
							<td border="1">' . $entity['VIN_NO'] . '</td>
							<td border="1">' . $entity['ENGINE_NO'] . '</td>
						</tr>';
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
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr[0], '130', '', 30, 30, '', '', 'T', false, 300, '', false, false, 1, false, false, false);

				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr[1], '', '', 30, 30, '', '', 'T', false, 300, 'R', false, false, 1, false, false, false);

				/*$pdf->SetXY(135, 80);
				$pdf->Write(0, '1');
				$pdf->Cell(0, 0, 'One', 0, $ln=0, 'C', 0, 'C', 0, false, 'B', 'B');*/
			}
			else
			{
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr, '', '', 30, 30, '', '', 'T', false, 300, 'R', false, false, 1, false, false, false);
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
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr[0], '130', '', 30, 30, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr[1], '', '', 30, 30, '', '', '', false, 300, 'R', false, false, 1, false, false, false);
			}
			else
			{
				$pdf->Image(FCPATH . '/resources/images/qr/' . $qr, '', '', 30, 30, '', '', 'T', false, 300, 'R', false, false, 1, false, false, false);
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

	/*public function run_migration()
	{
		echo '<pre>';
		$this->vin_engine_model->migrateItems();
		echo '</pre>'; die;
	}*/

}