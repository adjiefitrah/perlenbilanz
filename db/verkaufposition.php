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

namespace OCA\Perlenbilanz\Db;

use OCA\AppFramework\Db\Entity;


class VerkaufPosition extends Entity {

	public $id;
	public $vkId;
	public $pos;
	public $datum;
	public $stueck;
	public $typ;
	public $bezeichnung;
	public $geliefert;
	public $brutto;
	public $mwst;
	public $mwstProzent;
	public $netto;

	public function __construct(){
		$this->addType('vkId', 'int');
		$this->addType('pos', 'int');
		$this->addType('stueck', 'int');
		$this->addType('geliefert', 'bool');
		$this->addType('brutto', 'float');
		$this->addType('mwst', 'float');
		$this->addType('mwstProzent', 'float');
		$this->addType('netto', 'float');
	}

	/**
	 * @param $json
	 * @param $allProperties bool if true properties from the entity missing in the json will be set to null
	 * @return VerkaufPosition
	 */
	public static function fromJSON($json, $allProperties = false){
		$entity = new VerkaufPosition();
		if ($allProperties) {
			$entityProperties = get_object_vars($entity);
			foreach ($entityProperties as $prop => $value) {
				if ( ! is_null($json[$prop]) ) {
					$entity->$prop = $json[$prop];
				} else {
					$entity->$prop = null;
				}
				$entity->markFieldUpdated($prop);
			}
		} else {
			foreach($json as $prop => $value){
				if (property_exists($entity, $prop)) {
					$entity->$prop = $value;
					$entity->markFieldUpdated($prop);
				}
			}
		}
		return $entity;
	}

}