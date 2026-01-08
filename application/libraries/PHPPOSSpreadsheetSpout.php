<?php
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Common\Type;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class PHPPOSSpreadsheetSpout extends PHPPOSSpreadsheet
{
	private $reader;
	private $data;
	
	function __construct($inputFileName = NULL, $type='xlsx')
	{
		if ($inputFileName)
		{
			$CI =& get_instance();
			
			if (strtolower($type) == 'xlsx')
			{
				$this->reader = ReaderEntityFactory::createXLSXReader();
			}
			else
			{
				$this->reader = ReaderEntityFactory::createCSVReader();
			}
	
			$this->reader->open($inputFileName);
			
			$this->data = array();
			foreach ($this->reader->getSheetIterator() as $sheet) 
			{
				foreach($sheet->getRowIterator() as $row)
				{
					$this->data[] = $row->toArray();
				}
				
				//only read first sheet
				break;
			}
		}
	}
	
	public static function getFirstRow($inputFileName, $type='xlsx')
	{
		$CI =& get_instance();
		if (strtolower($type) == 'xlsx')
		{
			$reader = ReaderEntityFactory::createXLSXReader();
		}
		else
		{
			$reader = ReaderEntityFactory::createCSVReader();
		}

		$reader->open($inputFileName);
		foreach ($reader->getSheetIterator() as $sheet) 
		{
			foreach($sheet->getRowIterator() as $row)
			{
				//only need 1st row
				return $row->toArray();
			}
			
			//Empty spreadsheet
			return array();
		}
	}
	
	//$column starts at 0 and row starts at 1
	public function getCellByColumnAndRow($column, $row)
	{
		if ($this->data)
		{
			if (isset($this->data[$row-1][$column]))
			{
				//Do minus 0 as our rows start at 0
				return $this->data[$row-1][$column];	
			}
		}
		
		return NULL;
		
	}
	public function getNumberOfRows()
	{
		if ($this->data)
		{
			return count($this->data);
		}
		
		return null;
	}
		
	//$data is a matrix to export to excel
	public function arrayToSpreadsheet($arr,$filename, $is_report = false,$temp_export_filename = NULL)
	{
		$CI =& get_instance();

		
		if ($is_report)
		{
			define('SPOUT_EXCEL_WRITER_CELL_FORMAT',0);
		}
		else
		{
			//If we are NOT a report make sure we set text format to 49 (Text format for excel imports)
			define('SPOUT_EXCEL_WRITER_CELL_FORMAT',49);
		}
		if ($CI->config->item('spreadsheet_format') == 'XLSX')
		{
			$writer = WriterEntityFactory::createXLSXWriter(); // for XLSX files				
		}
		else
		{
			$writer = WriterEntityFactory::createCSVWriter(); // for CSV files
		}
		
		if (method_exists($writer,'setTempFolder'))
		{
			$writer->setTempFolder(ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir());
		}
		
		if ($temp_export_filename)
		{
			$tmpFilename = tempnam(ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir(), 'iexcel');
			$writer->openToFile($tmpFilename);
		}
		else
		{
			$writer->openToBrowser($filename); // stream data directly to the browser
		}
		if ($is_report)
		{
			
			for($k = 0;$k < count($arr);$k++)
			{
				for($j = 0;$j < count($arr[$k]); $j++)
				{
					$arr[$k][$j] = $this->stripCurrency($arr[$k][$j]);
					
					$hasleading_zero = substr($arr[$k][$j],0,1) == '0';
					
					if (is_numeric($row[$k]) && !$hasleading_zero && strlen($row[$k]) < 15)
					{
						$arr[$k][$j] = (double)$arr[$k][$j];
					}
				}
			}
		}
		
		foreach($arr as $row)
		{
			$writer->addRow(WriterEntityFactory::createRowFromArray($row));		
		}		
	
		$writer->close();
		
		if ($temp_export_filename)
		{
			if (!$CI->Appfile->does_file_exists($temp_export_filename))
			{
	    		$file_id = $CI->Appfile->save($temp_export_filename, file_get_contents($tmpFilename),'+ 7 days');
				$CI->session->set_userdata('recent_excel_export_file_id',$file_id);
				
			}
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			readfile($tmpFilename);
			exit();
		}		
		
		return NULL;
	}
}	
?>