<?php

/**
 * ownCloud - Perlenbilanz
 *
 * @author Jörn Friedrich Dreyer
 * @copyright 2013 Jörn Friedrich Dreyer jfd@butonic.de
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Perlenbilanz\Controller;

use OCA\AppFramework\Controller\Controller;
use OCA\AppFramework\Core\API;
use OCA\AppFramework\Http\Request;

use OCA\Perlenbilanz\Db\Einkauf;
use OCA\Perlenbilanz\Db\EinkaufMapper;
use OCA\Perlenbilanz\Db\Verkauf;
use OCA\Perlenbilanz\Db\VerkaufMapper;


class ReportController extends Controller {

	private $einkaufMapper;
	private $verkaufMapper;
	private $columns = Array ('A','B','C','D','E','F','G','H','I','J','K');

	public function __construct(API $api, Request $request, EinkaufMapper $einkaufMapper, VerkaufMapper $verkaufMapper){
		parent::__construct($api, $request);
		$this->einkaufMapper = $einkaufMapper;
		$this->verkaufMapper = $verkaufMapper;
	}

	private function addRow($sheet, $row, $e, $year) {
		
		$showMwStAndNetto = $year < 2014;
		
		//EK
		if ($e instanceof Einkauf) {
			$e->brutto=$e->brutto * -1;
			$e->mwst=$e->mwst * -1;
			$e->netto=$e->netto * -1;
		}
		
		$col = 1; //start at B
		// Refnr., Zahlweise, Wertstellung, Name, Positionstyp, Brutto, MwSt, Netto 
		$sheet->SetCellValue($this->columns[$col++].$row, $e->zahlweise);
		$sheet->SetCellValue($this->columns[$col++].$row, $e->wertstellung);
		$sheet->SetCellValue($this->columns[$col++].$row, $e->name);
		$sheet->SetCellValue($this->columns[$col++].$row, $e->typ); //FIXME
		$sheet->SetCellValue($this->columns[$col].$row, $e->brutto);
		$sheet->getStyle($this->columns[$col++].$row)->getNumberFormat()->setFormatCode('#,##0.00€');
		if($showMwStAndNetto) {
			$sheet->SetCellValue($this->columns[$col].$row, $e->mwst);
			$sheet->getStyle($this->columns[$col++].$row)->getNumberFormat()->setFormatCode('#,##0.00€');
			$sheet->SetCellValue($this->columns[$col].$row, $e->netto);
			$sheet->getStyle($this->columns[$col++].$row)->getNumberFormat()->setFormatCode('#,##0.00€');
		}
		
		// VK
		if ($e instanceof Verkauf) {
			if ($e->rechnungsjahr) {
				$rechnungsnummer = $e->rechnungsjahr . '-' . $e->rechnungsnummer;
			} else {
				$rechnungsnummer = $e->rechnungsnummer;
			}
			$sheet->SetCellValue($this->columns[$col++].$row, $rechnungsnummer);
			$sheet->SetCellValue($this->columns[$col].$row, 'http://oc.butonic.de/index.php/apps/perlenbilanz/#/verkauf/'.$e->id);
			$sheet->getCell($this->columns[$col++].$row)->getHyperlink()->setUrl('http://oc.butonic.de/index.php/apps/perlenbilanz/#/verkauf/'.$e->id);
		}
		if ($e instanceof Einkauf) {
			$sheet->SetCellValue($this->columns[++$col].$row, 'http://oc.butonic.de/index.php/apps/perlenbilanz/#/einkauf/'.$e->id);
			$sheet->getCell($this->columns[$col++].$row)->getHyperlink()->setUrl('http://oc.butonic.de/index.php/apps/perlenbilanz/#/einkauf/'.$e->id);
		}
	}
	
	/**
	 * @param PHPExcel_Worksheet $sheet
	 * @param int $row
	 * @param \OCA\Perlenbilanz\Db\Verkauf[]|\OCA\Perlenbilanz\Db\Einkauf[] $entities
	 * @param int $year
	 */
	private function addSection ($sheet, &$row, $entities, $year) {
		
		$showMwStAndNetto = $year < 2014;
		
		//sortieren nach wertstellung, zahlweise
		usort($entities, function ($a, $b) {
			if ($a->wertstellung == $b->wertstellung) {
				if ($a->zahlweise == $b->zahlweise) {
					return 0; //TODO
				}
				return ($a->zahlweise < $b->zahlweise) ? -1 : 1;
			}
			return ($a->wertstellung < $b->wertstellung) ? -1 : 1;
		});

		// Add columns
		// Zahlweise, Wertstellung, Brutto, Name, Typ (umbenennen in Positionstyp), MwSt, Netto

		$col = 0;
		$sheet->SetCellValue($this->columns[$col++].$row, 'Refnr.');
		$sheet->SetCellValue($this->columns[$col++].$row, 'Zahlweise');
		$sheet->SetCellValue($this->columns[$col++].$row, 'Wertstellung');
		$sheet->SetCellValue($this->columns[$col++].$row, 'Name');
		$sheet->SetCellValue($this->columns[$col++].$row, 'Positionstyp');
		$sheet->SetCellValue($this->columns[$col++].$row, 'Brutto');
		if($showMwStAndNetto) {
			$sheet->SetCellValue($this->columns[$col++].$row, 'MwSt');
			$sheet->SetCellValue($this->columns[$col++].$row, 'Netto');
		}
		// VK
		if (isset($entities[0]) && $entities[0] instanceof Verkauf) {
			$sheet->SetCellValue($this->columns[$col].$row, 'Rechnung');
		}
		$sheet->SetCellValue($this->columns[++$col].$row, 'ID');

		$col = 0;
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);
		$sheet->getStyle($this->columns[$col++].$row)->getFont()->setBold(true);

		$row++;

		//add rows
		foreach ($entities as $entity) {
			$this->addRow($sheet, $row++, $entity, $year);
		}
	}
	
	/**
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 * @CSRFExemption
	 */
	public function renderReport() {
		$params = $this->getParams();
		$user = $this->api->getUserId();
		
		$year = $params['year'];
		
		$showMwStAndNetto = $year < 2014;
		
		$month = $params['month'];

		if (! $year || ! $month) {
			http_response_code(404);
			return;
		}
		/** Error reporting */
		error_reporting(E_ALL);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename=Buchungsbericht_'.$year.$month.'.xlsx');
		// Create new PHPExcel object
		//echo date('H:i:s') . " Create new PHPExcel object\n";
		$objPHPExcel = new \PHPExcel();

		// Set properties
		//echo date('H:i:s') . " Set properties\n";
		$objPHPExcel->getProperties()->setCreator("Perlenbilanz");
		//$objPHPExcel->getProperties()->setLastModifiedBy("Maarten Balliauw");
		$objPHPExcel->getProperties()->setTitle('Buchungsbericht '.$year.'-'.$month);
		$objPHPExcel->getProperties()->setSubject('Buchungsbericht '.$year.'-'.$month);
		$objPHPExcel->getProperties()->setDescription('Buchhaltungsbericht basierend auf '.$year.'-'.$month);


		$start = $year.'-'.$month.'-01';
		$d = new \DateTime( $start );
		$end = $d->format( 'Y-m-t' );
		$row = 1;
		
		
		//get Einkäufe from DB
		//$verkaeufe = $this->verkaufMapper->report($start, $end, $this->api->getUserId());
		//$einkaeufe = $this->einkaufMapper->report($start, $end, 'ninaobermeyer');
		//$verkaeufe = $this->verkaufMapper->report($start, $end, 'ninaobermeyer');
		//$entities = array_merge($einkaeufe, $verkaeufe);
				
		$sheet = $objPHPExcel->getActiveSheet();

		$einkaeufe = $this->einkaufMapper->report($start, $end, $user);
		\OCP\Util::writeLog('perlenbilanz','found '.sizeof($einkaeufe).' einkäufe',\OCP\Util::DEBUG);

		// add headline
		$sheet->SetCellValue('A'.$row, 'EINKÄUFE');
		$sheet->getStyle('A'.$row)->getFont()->setBold(true);
		$row++;
		
		if (sizeof($einkaeufe)>0) {
			$sumstartrow = $row + 1;

			$this->addSection($sheet, $row, $einkaeufe, $year);

			$sumendrow = $row - 1;

			//add sum
			$sheet->SetCellValue('A'.$row, 'Summe');
			$sheet->getStyle('A'.$row)->getFont()->setBold(true);
			$sheet->SetCellValue('F'.$row, '=SUM(F'.$sumstartrow.':F'.$sumendrow.')');
			$sheet->getStyle('F'.$row)->getNumberFormat()->setFormatCode('#,##0.00€');
			$sheet->getStyle('F'.$row)->getFont()->setBold(true);
			if($showMwStAndNetto) {
				$sheet->SetCellValue('G'.$row, '=SUM(G'.$sumstartrow.':G'.$sumendrow.')');
				$sheet->getStyle('G'.$row)->getNumberFormat()->setFormatCode('#,##0.00€');
				$sheet->getStyle('G'.$row)->getFont()->setBold(true);
				$sheet->SetCellValue('H'.$row, '=SUM(H'.$sumstartrow.':H'.$sumendrow.')');
				$sheet->getStyle('H'.$row)->getNumberFormat()->setFormatCode('#,##0.00€');
				$sheet->getStyle('H'.$row)->getFont()->setBold(true);
			}
		} else {
			$sheet->SetCellValue('A'.$row, 'Keine Einkäufe');
		}
		
		// add empty line
		$row+=2;
		
		$verkaeufe = $this->verkaufMapper->report($start, $end, $user);
		\OCP\Util::writeLog('perlenbilanz','found '.sizeof($verkaeufe).' verkäufe',\OCP\Util::DEBUG);
		
		// add headline
		$sheet->SetCellValue('A'.$row, 'VERKÄUFE');
		$sheet->getStyle('A'.$row)->getFont()->setBold(true);
		$row++;
		
		if (sizeof($einkaeufe)>0) {
			
			$sumstartrow = $row + 1;

			$this->addSection($sheet, $row, $verkaeufe, $year);

			$sumendrow = $row - 1;

			//add sum
			$sheet->SetCellValue('A'.$row, 'Summe');
			$sheet->getStyle('A'.$row)->getFont()->setBold(true);
			$sheet->SetCellValue('F'.$row, '=SUM(F'.$sumstartrow.':F'.$sumendrow.')');
			$sheet->getStyle('F'.$row)->getNumberFormat()->setFormatCode('#,##0.00€');
			$sheet->getStyle('F'.$row)->getFont()->setBold(true);
			if($showMwStAndNetto) {
				$sheet->SetCellValue('G'.$row, '=SUM(G'.$sumstartrow.':G'.$sumendrow.')');
				$sheet->getStyle('G'.$row)->getNumberFormat()->setFormatCode('#,##0.00€');
				$sheet->getStyle('G'.$row)->getFont()->setBold(true);
				$sheet->SetCellValue('H'.$row, '=SUM(H'.$sumstartrow.':H'.$sumendrow.')');
				$sheet->getStyle('H'.$row)->getNumberFormat()->setFormatCode('#,##0.00€');
				$sheet->getStyle('H'.$row)->getFont()->setBold(true);
			}
		
		} else {
			$sheet->SetCellValue('A'.$row, 'Keine Verkäufe');
		}
		
		// calculate optimal column width
		
		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);
		$sheet->getColumnDimension('G')->setAutoSize(true);
		$sheet->getColumnDimension('H')->setAutoSize(true);
		$sheet->getColumnDimension('I')->setAutoSize(true);
		$sheet->getColumnDimension('J')->setAutoSize(true);
		
		// Rename sheet
		//echo date('H:i:s') . " Rename sheet\n";
		//$objPHPExcel->getActiveSheet()->setTitle('Einkäufe');


		// Save Excel 2007 file
		//echo date('H:i:s') . " Write to Excel2007 format\n";
		$objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');

		// Echo done
		//echo date('H:i:s') . " Done writing file.\r\n";
	}

}
