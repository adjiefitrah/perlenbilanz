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

use OCA\Perlenbilanz\Db\VerkaufMapper;
use OCA\Perlenbilanz\Db\VerkaufPositionMapper;
use OCA\Perlenbilanz\Http\JSONResponse;


class InvoiceController extends Controller {

	private $verkaufMapper;
	private $verkaufPositionenMapper;

	public function __construct(API $api, Request $request, VerkaufMapper $verkaufMapper, VerkaufPositionMapper $verkaufPositionenMapper){
		parent::__construct($api, $request);
		$this->verkaufMapper = $verkaufMapper;
		$this->verkaufPositionenMapper = $verkaufPositionenMapper;
	}

	public function getRechnungsname(\OCA\Perlenbilanz\Db\Verkauf $verkauf) {
		return 'Rechnung '.$verkauf->rechnungsjahr.'-'.$verkauf->rechnungsnummer;
	}
	public function getRechnungspath(\OCA\Perlenbilanz\Db\Verkauf $verkauf = null) {
		return '/Perlenbilanz/Rechnungen';
	}
	
	/**
	 * @Ajax
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 */
	public function renderInvoice(){

		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);

		//TODO check valid params?
		try {
			$verkauf = $this->verkaufMapper->find($json['vkid'], $this->api->getUserId());

			$positionen = $this->verkaufPositionenMapper->findAll($verkauf->id,$verkauf->userid);
		} catch (\OCA\AppFramework\Db\DoesNotExistException $e) {
			$response = new \OCA\Perlenbilanz\Http\JSONResponse();
			$response->setStatus(\OCA\AppFramework\Http\Http::STATUS_NOT_FOUND);
			return $response;
		}
		
		$brutto = 0;
		$netto = 0;
		foreach ($positionen as $position) {
			$brutto += $position->stueck * $position->brutto;
			$netto += $position->netto;
			$position->brutto = $position->brutto;
			$position->bezeichnung = str_replace("\n", "<br/>\n", \OC_Util::sanitizeHTML($position->bezeichnung));
		}
		$mwst = $brutto - $netto;
		$verkauf->brutto = $brutto;
		$verkauf->netto = $netto;
		$verkauf->mwst = $mwst;
		
		$verkauf->rechnungsanschrift = str_replace("\n", "<br/>\n", \OC_Util::sanitizeHTML($verkauf->rechnungsanschrift));
		
		$dtw = new \DateTime($verkauf->wertstellung);
		$verkauf->wertstellung = $dtw->format( 'd.m.Y' );
		
		/** Error reporting */
		error_reporting(E_ALL);
		
		

		
		$dt = new \DateTime();
		$date = $dt->format( 'd.m.Y' );
		
		//create app folder if it does not yet exist
		if ( ! \OC\Files\Filesystem::is_dir('Perlenbilanz') ) {
			\OC\Files\Filesystem::mkdir('Perlenbilanz');
		}
		if ( ! \OC\Files\Filesystem::is_dir($this->getRechnungspath()) ) {
			\OC\Files\Filesystem::mkdir($this->getRechnungspath());
		}
		//if ( ! \OC\Files\Filesystem::is_file('Perlenbilanz/Rechnungen/Vorlage.php') ) {
			$template = file_get_contents(__DIR__ . '/../templates/invoice.php');
			\OC\Files\Filesystem::file_put_contents($this->getRechnungspath().'/Vorlage.php', $template);
		//}
		
		$invoiceTemplate = new \OCA\Perlenbilanz\Http\Template($this->getRechnungspath().'/Vorlage.php');
		$invoiceTemplate->assign('datum', $date);
		$invoiceTemplate->assign('verkauf', $verkauf);
		$invoiceTemplate->assign('positionen', $positionen);
		$html = $invoiceTemplate->fetchPage();
		
		$filename = $this->getRechnungsname($verkauf);
		
		\OC\Files\Filesystem::file_put_contents($this->getRechnungspath().'/'.$filename.'.html', $html);
		
		/*
		require_once __DIR__ . '/../3rdparty/MPDF56/mpdf.php';
		$mpdf=new \mPDF();
		
		$mpdf->WriteHTML($html);
		$pdf = $mpdf->Output(null, 'S');
		$path = $this->getRechnungspath().'/'.$filename.'.pdf';
		\OC\Files\Filesystem::file_put_contents($path, $pdf);
		*/
		
		//return complete object back
		return $this->renderRawJSON();
		
		//$response = new FileDownloadResponse($path, 'application/pdf');
		//return $response;
		
		/*
		header('Content-Type: application/pdf');
		//TODO jahr-rechnungsnummer?
		header('Content-Disposition: attachment; filename=Rechnung_'.$verkauf->rechnungsnummer.'.pdf');
		//header('Content-Disposition: attachment; filename=Rechnung.pdf');
		
		\OC\Files\Filesystem::readfile($path);
		
		 */
	}
	
	/**
	 * @Ajax
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 */
	public function deleteInvoice(){

		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);

		//TODO check valid params?
		try {
			$verkauf = $this->verkaufMapper->find($json['id'], $this->api->getUserId());
		} catch (\OCA\AppFramework\Db\DoesNotExistException $e) {
			$response = new \OCA\Perlenbilanz\Http\JSONResponse();
			$response->setStatus(\OCA\AppFramework\Http\Http::STATUS_NOT_FOUND);
			return $response;
		}
		
		$filename = $this->getRechnungsname($verkauf);
		\OC\Files\Filesystem::unlink($this->getRechnungspath().'/'.$filename.'.html');
		\OC\Files\Filesystem::unlink($this->getRechnungspath().'/'.$filename.'.pdf');
		
		//remove rechnungsnummer
		$verkauf->setRechnungsnummer(null);
		//TODO remove rechnungsjahr?
		
		$this->verkaufMapper->update($verkauf);
		
		return $this->renderRawJSON($verkauf);
	}
	/**
	 * use a minimal JSONResponse implementation that renders the $data as JSON
	 * Unlike the AppFramework JSONResponse it is not wrapped in {'status':'success','data':$data}
	 * @param array $data will be rendered as JSON with JSON_FORCE_OBJECT
	 * @return \OCA\AppFramework\Http\JSONResponse|JSONResponse
	 */
	public function renderRawJSON($data=array(), $options = JSON_FORCE_OBJECT){
		$response = new JSONResponse();
		$response->setData($data, $options);
		return $response;
	}

	public function validateJSON($input){
		$validJSON = json_decode($input, true);
		if (json_last_error() === JSON_ERROR_NONE) {
			return $validJSON;
		} else {
			http_response_code(404);
			$this->renderRawJSON(array("error"=>"bad_request","reason"=>"invalid_json"));
		}
	}
}
