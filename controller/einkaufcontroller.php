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

use OCA\Perlenbilanz\Db\Einkauf;
use OCA\Perlenbilanz\Db\EinkaufMapper;
use OCA\Perlenbilanz\Http\JSONResponse;


class EinkaufController extends Controller {

	private $mapper;

	/**
	 * @param Request $request: an instance of the request
	 * @param API $api: an api wrapper instance
	 * @param EinkaufMapper $mapper: an itemwrapper instance
	 */
	public function __construct(API $api, Request $request, EinkaufMapper $mapper){
		parent::__construct($api, $request);
		$this->mapper = $mapper;
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
	public function listEinkaeufe(){
		$params = $this->getParams();

		if (isset($params['list'])) {
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
			$list = $this->mapper->missingWertstellung($this->api->getUserId());
			return $this->renderRawJSON($list);
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
	public function getEinkauf(){
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
	public function postEinkauf(){

		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);

		//set user
		$json['userid'] = $this->api->getUserId();

		//TODO check valid?
		$entity = Einkauf::fromJSON($json);
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
	public function putEinkauf(){

		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);

		//set user
		$json['userid'] = $this->api->getUserId();

		//TODO check valid?
		$entity = Einkauf::fromJSON($json, true);
		$this->mapper->update($entity);

		//return complete object back
		return $this->renderRawJSON($entity);
	}

}
