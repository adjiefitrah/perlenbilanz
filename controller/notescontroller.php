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
use OCA\Perlenbilanz\AppFramework\Core\API;
use OCA\Perlenbilanz\AppFramework\Http\Request;
use OCA\Perlenbilanz\Http\JSONResponse;

class NotesController extends Controller {

    public function __construct(API $api, Request $request){
        parent::__construct($api, $request);
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
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 * @CSRFExemption
	 */
	public function getNotes() {
		$notes = $this->api->getUserValue('notes');
		$response = new JSONResponse();
		$response->setData(array('text'=>$notes));
		return $response;
	}

	/**
	 * @IsAdminExemption
	 * @IsSubAdminExemption
	 * @CSRFExemption
	 */
	public function saveNotes() {
		//read json from input
		$input = file_get_contents("php://input");
		$json = $this->validateJSON($input);
		$notes = $json['text'];
		$this->api->setUserValue('notes', $notes);
		$response = new JSONResponse();
		$response->setData(array('ok'=>true));
		return $response;
	}

}
