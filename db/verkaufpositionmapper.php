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

use \OCA\AppFramework\Core\API;
use \OCA\AppFramework\Db\Mapper;
use \OCA\AppFramework\Db\DoesNotExistException;


class VerkaufPositionMapper extends Mapper {

	/**
	 * @param API $api: Instance of the API abstraction layer
	 */
	public function __construct(API $api){
		parent::__construct($api, 'pb_vk_positionen');
	}

	/**
	 * Finds all VerkaufPosition
	 * @return VerkaufVerkaufPosition[]
	 */
	public function findAll($vkId, $userid){

		$sql = 'SELECT `' . $this->getTableName() .'`.*
				FROM `' . $this->getTableName() .'`
				JOIN `*PREFIX*pb_vk_verkaeufe`
				ON `' . $this->getTableName() . '`.`vk_id`=`*PREFIX*pb_vk_verkaeufe`.`id`
				WHERE `' . $this->getTableName() . '`.`vk_id` = ?
				AND `*PREFIX*pb_vk_verkaeufe`.`userid` = ?
				ORDER BY `' . $this->getTableName() . '`.`pos` ASC, `' . $this->getTableName() . '`.`vk_id` ASC';

		$result = $this->execute($sql,array($vkId, $userid));

		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new VerkaufPosition();
			$entity->fromRow($row);
			array_push($entityList, $entity);
		}

		return $entityList;
	}

}