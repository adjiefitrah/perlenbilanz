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
use OCA\Perlenbilanz\AppFramework\Http\Request;

use OCA\Perlenbilanz\Db\VerkaufMapper;
use OCA\Perlenbilanz\Db\VerkaufPosition;
use OCA\Perlenbilanz\Db\VerkaufPositionMapper;
use OCA\Perlenbilanz\Http\JSONResponse;


class VerkaufPositionController extends Controller {

	/**
	 * @var \OCA\Perlenbilanz\Db\VerkaufPositionMapper
	 */
	private $posMapper;
	/**
	 * @var \OCA\Perlenbilanz\Db\VerkaufMapper
	 */
	private $vkMapper;

	/**
	 * @param Request $request: an instance of the request
	 * @param API $api: an api wrapper instance
	 * @param VerkaufPositionMapper $posMapper
	 * @param VerkaufMapper $vkMapper
	 */
	public function __construct(API $api, Request $request, VerkaufPositionMapper $posMapper, VerkaufMapper $vkMapper){
		parent::__construct($api, $request);
		$this->posMapper = $posMapper;
		$this->vkMapper = $vkMapper;
	}

	/**
	 * use a minimal JSONResponse implementation that renders the $data as JSON
	 * Unlike the AppFramework JSONResponse it is not wrapped in {'status':'success','data':$data}
	 * @param array $data will be rendered as JSON
	 * @return \OCA\Perlenbilanz\AppFramework\Http\JSONResponse
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
		if (isset($params['vkId'])) {
			$entities = $this->posMapper->findAll($params['vkId'],$this->api->getUserId());
			return $this->renderRawJSON($entities, null);
		} else {
			http_response_code(404);
			$this->renderRawJSON(array("error"=>"bad_request","reason"=>"vkId missing"));
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

		//TODO check valid?
		$entity = VerkaufPosition::fromJSON($json);
		try {
			// vk_id owned by user? will throw DoesNotExistException
			$this->vkMapper->find($entity->vkId, $this->api->getUserId());

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
		$entity = VerkaufPosition::fromJSON($json, true);
		try {
			// vk_id owned by user? will throw DoesNotExistException
			$this->vkMapper->find($entity->vkId, $this->api->getUserId());

			$entity = $this->posMapper->update($entity);

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
	 */
	public function deletePosition(){

		
		$params = $this->getParams();
		if (isset($params['id'])) {

			//TODO check valid?
			$entity = new VerkaufPosition();
			$entity->setId($params['id']);

			$this->posMapper->delete($entity);

			//return ok?
			return $this->renderRawJSON();
		} else {
			return new \OCA\Perlenbilanz\AppFramework\Http\NotFoundResponse();
		}

	}

}
