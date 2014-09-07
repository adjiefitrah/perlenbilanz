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

use \OCA\Perlenbilanz\AppFramework\Core\API;
use \OCA\Perlenbilanz\AppFramework\Db\Mapper;


class EinkaufPositionMapper extends Mapper {

	/**
	 * @param API $api: Instance of the API abstraction layer
	 */
	public function __construct(API $api){
		parent::__construct($api, 'pb_ek_positionen');
	}

	/**
	 * Finds all EinkaufPosition
	 * @return EinkaufPosition[]
	 */
	public function findAll($ekId, $userid){

		$sql = 'SELECT `' . $this->getTableName() .'`.*
			FROM `' . $this->getTableName() .'`
			JOIN `*PREFIX*pb_ek_einkaeufe`
			ON `' . $this->getTableName() . '`.`ek_id`=`*PREFIX*pb_ek_einkaeufe`.`id`
			WHERE `' . $this->getTableName() . '`.`ek_id` = ?
			AND `*PREFIX*pb_ek_einkaeufe`.`userid` = ?';

		$result = $this->execute($sql,array($ekId, $userid));

		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new EinkaufPosition();
			$entity->fromRow($row);
			array_push($entityList, $entity);
		}

		return $entityList;
	}

	/**
	 * Finds all EinkaufPosition
	 * @return EinkaufPosition[]
	 */
	public function findOpen($userid){

		$sql = 'SELECT `' . $this->getTableName() .'`.*
				FROM `' . $this->getTableName() . '`
				JOIN `*PREFIX*pb_ek_einkaeufe`
				ON `' . $this->getTableName() . '`.`ek_id`=`*PREFIX*pb_ek_einkaeufe`.`id`
				WHERE `geliefert` != ?
				AND `userid` = ?';

		$result = $this->execute($sql,array(true, $userid));

		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new EinkaufPosition();
			$entity->fromRow($row);
			array_push($entityList, $entity);
		}

		return $entityList;
	}

}