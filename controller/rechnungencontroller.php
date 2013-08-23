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


class RechnungenController extends Controller {

	private $verkaufMapper;

	public function __construct(API $api, Request $request, VerkaufMapper $verkaufMapper){
		parent::__construct($api, $request);
		$this->verkaufMapper = $verkaufMapper;
	}

	public function generateHTML() {
		
	}
	public function generatePDF() {
		
	}
	
	// Format 123
	//        <fortlaufende Nummer, dreistellig>
	// obwohl rechnungsnummern eigentlich nicht "fortlaufend" sein müssten:
	// vgl. http://www.steuer-schutzbrief.de/steuertipp-rubriken/steuer-tipps/artikel/rechnungsnummern-muessen-nicht-aufeinanderfolgend-aber-einmalig-sein.html
	public function suggestID() {
		//liste aller IDs aus der rechnungstabelle, MAX+1
		//freigegebene ids?
	}

}
