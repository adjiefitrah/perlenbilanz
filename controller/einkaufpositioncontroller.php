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

use OCA\Perlenbilanz\AppFramework\Controller\Controller;
use OCA\Perlenbilanz\AppFramework\Db\DoesNotExistException;
use OCA\Perlenbilanz\AppFramework\Core\API;
use OCA\Perlenbilanz\AppFramework\Http\ForbiddenResponse;
use OCA\Perlenbilanz\AppFramework\Http\Request;

use OCA\Perlenbilanz\Db\EinkaufMapper;
use OCA\Perlenbilanz\Db\EinkaufPosition;
use OCA\Perlenbilanz\Db\EinkaufPositionMapper;
use OCA\Perlenbilanz\Http\JSONResponse;


class EinkaufPositionController extends Controller {

	/**
	 * @var \OCA\Perlenbilanz\Db\EinkaufPositionMapper
	 */
	private $posMapper;
	/**
	 * @var \OCA\Perlenbilanz\Db\EinkaufMapper
	 */
	private $ekMapper;

	/**
	 * @param Request $request: an instance of the request
	 * @param API $api: an api wrapper instance
	 * @param EinkaufPositionMapper $mapper: an itemwrapper instance
	 */
	public function __construct(API $api, Request $request, EinkaufPositionMapper $posMapper, EinkaufMapper $ekMapper){
		parent::__construct($api, $request);
		$this->posMapper = $posMapper;
		$this->ekMapper = $ekMapper;
	}

	/**
	 * use a minimal JSONResponse implementation that renders the $data as JSON
	 * Unlike the AppFramework JSONResponse it is not wrapped in {'status':'success','data':$data}
	 * @param array $data will be rendered as JSON
	 * @return JSONResponse
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
	public function listPositionen(){
		$params = $this->getParams();
		if (isset($params['ekId'])) {
			$entities = $this->posMapper->findAll($params['ekId'], $this->api->getUserId());
			return $this->renderRawJSON($entities, null, 0);
		} else if (isset($params['geliefert']) && $params['geliefert'] == 'false') {
			$entities = $this->posMapper->findOpen($this->api->getUserId());
			return $this->renderRawJSON($entities, null, 0);
		} else {
			http_response_code(404);
			$this->renderRawJSON(array("error"=>"bad_request","reason"=>"ekId missing"));
		}

	}

	/**
	 * @Ajax
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * @return an instance of a Response implementation
	 */
	public function postPosition(){

		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);

		//set ek_id
		/*
		$params = $this->getParams();
		$json['ekId'] = $params['ekId'];
*/
		//TODO check valid?
		$entity = EinkaufPosition::fromJSON($json);
		try {
			// ek_id owned by user? will throw DoesNotExistException
			$this->ekMapper->find($entity->ekId, $this->api->getUserId());

			$entity = $this->posMapper->insert($entity);

			//return complete object back
			return $this->renderRawJSON($entity);
		} catch (DoesNotExistException $ex) {
			return new ForbiddenResponse();
		}

	}

	/**
	 * @Ajax
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 *
	 * @return an instance of a Response implementation
	 */
	public function putPosition(){

		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);

		//TODO check valid?
		$entity = EinkaufPosition::fromJSON($json, true);
		try {
			// ek_id owned by user? will throw DoesNotExistException
			$this->ekMapper->find($entity->ekId, $this->api->getUserId());

			$entity = $this->posMapper->update($entity);

			//return complete object back
			return $this->renderRawJSON($entity);
		} catch (DoesNotExistException $ex) {
			return new ForbiddenResponse();
		}
	}


}
