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
use OCA\AppFramework\Db\DoesNotExistException;
use OCA\AppFramework\Core\API;
use OCA\AppFramework\Http\NotFoundResponse;
use OCA\AppFramework\Http\Request;

use OCA\Perlenbilanz\Db\Verkauf;
use OCA\Perlenbilanz\Db\VerkaufMapper;
use OCA\Perlenbilanz\Db\VerkaufPositionMapper;
use OCA\Perlenbilanz\Http\JSONResponse;


class VerkaufController extends Controller {

	private $mapper;

	/**
	 * @param Request $request: an instance of the request
	 * @param API $api: an api wrapper instance
	 * @param EinkaufMapper $mapper: an itemwrapper instance
	 */
	public function __construct(API $api, Request $request, VerkaufMapper $mapper, VerkaufPositionMapper $positionenMapper){
		parent::__construct($api, $request);
		$this->mapper = $mapper;
		$this->positionenMapper = $positionenMapper;
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

	/**
	 * @Ajax
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 */
	public function listVerkaeufe(){
		$params = $this->getParams();
		if (isset($params['geliefert']) && $params['geliefert'] == 'false') {
			$entities = $this->mapper->findOpen($this->api->getUserId());
			return $this->renderRawJSON($entities, null);
		} else if (isset($params['list'])) {
			if ($params['list']=='accounts') {
				$list = $this->mapper->listAccounts($this->api->getUserId());
			} else if ($params['list']=='names') {
				$list = $this->mapper->listNames($this->api->getUserId());
			} else {
				$list = array();
			}
			return $this->renderRawJSON($list);
		} else if (isset($params['guess'])) {
			if ($params['guess']=='account') {
				$list = $this->mapper->guessAccount($params['plattform'],$params['name'],$this->api->getUserId());
			} else if ($params['guess']=='name') {
				$list = $this->mapper->guessName($params['plattform'],$params['account'],$this->api->getUserId());
			} else {
				$list = array();
			}
			return $this->renderRawJSON($list);
		} else if (isset($params['wertstellung'])) {
			$entities = $this->mapper->missingWertstellung($this->api->getUserId());
			return $this->renderRawJSON($entities, null);
		} else if (isset($params['overview'])) {
			if ($params['overview'] === 'current') {
				$list = $this->mapper->overview($this->api->getUserId());
				return $this->renderRawJSON($list);
			} //TODO year and month. then without wertstellung = null
		}
		return new NotFoundResponse();
	}
	/**
	 * @Ajax
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 */
	public function getVerkauf(){
		$params = $this->getParams();

		try {
			$entity = $this->mapper->find($params['id'], $this->api->getUserId());
			return $this->renderRawJSON($entity);
		} catch (DoesNotExistException $ex) {
			return new NotFoundResponse();
		}
	}
	/**
	 * @Ajax
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * @return an instance of a Response implementation
	 */
	public function postVerkauf(){

		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);

		//set user
		$json['userid'] = $this->api->getUserId();

		//TODO check valid?
		$entity = Verkauf::fromJSON($json);
		$entity = $this->mapper->insert($entity);

		//return complete object back
		return $this->renderRawJSON($entity);
	}
	/**
	 * @Ajax
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * @return an instance of a Response implementation
	 */
	public function putVerkauf(){

		$params = $this->getParams();
		if (isset($params['render']) && $params['render'] === 'html') {
			$this->renderInvoice();
			exit();
		}
		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);

		//set user
		$json['userid'] = $this->api->getUserId();

		//TODO check valid?
		$entity = Verkauf::fromJSON($json, true);
		$this->mapper->update($entity);

		//return complete object back
		return $this->renderRawJSON($entity);
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

		//set user
		$json['userid'] = $this->api->getUserId();

		//TODO check valid?
		$verkauf = Verkauf::fromJSON($json, true);
		
		$positionen = $this->positionenMapper->findAll($verkauf->id,$verkauf->userid);
		
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
		
		
		require_once __DIR__ . '/../3rdparty/MPDF56/mpdf.php';

		
		$dt = new \DateTime();
		$date = $dt->format( 'd.m.Y' );
		
		//create app folder if it does not yet exist
		if ( ! \OC\Files\Filesystem::is_dir('Perlenbilanz') ) {
			\OC\Files\Filesystem::mkdir('Perlenbilanz');
		}
		if ( ! \OC\Files\Filesystem::is_dir('Perlenbilanz/Rechnungen') ) {
			\OC\Files\Filesystem::mkdir('Perlenbilanz/Rechnungen');
		}
		//if ( ! \OC\Files\Filesystem::is_file('Perlenbilanz/Rechnungen/Vorlage.php') ) {
			$template = file_get_contents(__DIR__ . '/../templates/invoice.php');
			\OC\Files\Filesystem::file_put_contents('Perlenbilanz/Rechnungen/Vorlage.php', $template);
		//}
		
		$invoiceTemplate = new \OCA\Perlenbilanz\Http\Template('Perlenbilanz/Rechnungen/Vorlage.php');
		$invoiceTemplate->assign('datum', $date);
		$invoiceTemplate->assign('verkauf', $verkauf);
		$invoiceTemplate->assign('positionen', $positionen);
		$html = $invoiceTemplate->fetchPage();
		
		\OC\Files\Filesystem::file_put_contents('Perlenbilanz/Rechnungen/Rechnung '.$verkauf->rechnungsnummer.'.html', $html);
		
		$mpdf=new \mPDF();
		
		header('Content-Type: application/pdf');
		//header('Content-Disposition: attachment; filename=Rechnung_'.$entity->rechnungsnummer.'.pdf');
		header('Content-Disposition: attachment; filename=Rechnung.pdf');
		
		$mpdf->WriteHTML($html);
		$pdf = $mpdf->Output(null, 'S');
		\OC\Files\Filesystem::file_put_contents('Perlenbilanz/Rechnungen/Rechnung '.$verkauf->rechnungsnummer.'.pdf', $pdf);
		
	}

}
