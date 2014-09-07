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


class VerkaufMapper extends Mapper {

	/**
	 * @param API $api: Instance of the API abstraction layer
	 */
	public function __construct(API $api){
		parent::__construct($api, 'pb_vk_verkaeufe');
	}


	/**
	 * Finds a verkauf by id
	 * @throws DoesNotExistException: if the item does not exist
	 * @throws MultipleObjectsReturnedException if more than one item exist
	 * @return Verkauf verkauf
	 */
	public function find($id, $userid){

		$sql = 'SELECT *
				FROM `' . $this->getTableName() . '`
				WHERE `id` = ?
					AND `userid` = ?';

		$params = array($id, $userid);

		$row = $this->findOneQuery($sql, $params);

		$entity = new Verkauf();
		$entity->fromRow($row);
		return $entity;
	}

	/**
	 * Finds all VerkaufPosition
	 * @return VerkaufPosition[]
	 */
	public function findOpen($userid){

		$sql = 'SELECT *,
				SUM(`stueck`*`brutto`) AS `brutto_total`
				FROM `' . $this->getTableName() .'`
				JOIN `*PREFIX*pb_vk_positionen`
				ON `' . $this->getTableName() . '`.`id`=`*PREFIX*pb_vk_positionen`.`vk_id`
				WHERE `geliefert` != ?
				AND `' . $this->getTableName() . '`.`userid` = ?
				GROUP BY `oc_pb_vk_positionen`.`vk_id`';

		$result = $this->execute($sql,array(true, $userid));

		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new VerkaufPosition();
			$entity->fromRow($row);
			array_push($entityList, $entity);
		}

		return $entityList;
	}

	/**
	 * @return []
	 */
	public function listAccounts($userid){

		$sql = 'SELECT DISTINCT `account`
				FROM `' . $this->getTableName() . '`
				WHERE `account` IS NOT NULL
					AND `userid` = ?
				ORDER BY `account`';

		$result = $this->execute($sql, array($userid));

		$accountList = array();
		while($row = $result->fetchRow()){
			array_push($accountList, $row['account']);
		}

		return $accountList;
	}
	/**
	 * @return []
	 */
	public function listNames($userid){

		$sql = 'SELECT DISTINCT `name`
				FROM `' . $this->getTableName() . '`
				WHERE `name` IS NOT NULL
					AND `userid` = ?
				ORDER BY `name`';

		$result = $this->execute($sql, array($userid));

		$nameList = array();
		while($row = $result->fetchRow()){
			array_push($nameList, $row['name']);
		}

		return $nameList;
	}

	/**
	 * @return []
	 */
	public function guessAccount($plattform, $name, $userid){

		$sql = 'SELECT DISTINCT `account`
				FROM `' . $this->getTableName() . '`
				WHERE `plattform` = ?
					AND `name` = ?
					AND `account` IS NOT NULL
					AND `userid` = ?';

		$result = $this->execute($sql,array($plattform, $name, $userid));

		$accountList = array();
		while($row = $result->fetchRow()){
			array_push($accountList, $row['account']);
		}

		return $accountList;
	}
	/**
	 * @return []
	 */
	public function guessName($plattform, $account, $userid){

		$sql = 'SELECT DISTINCT `name`
				FROM `' . $this->getTableName() . '`
				WHERE `plattform` = ?
					AND `account` = ?
					AND `name` IS NOT NULL
					AND `userid` = ?';

		$result = $this->execute($sql, array($plattform, $account, $userid));

		$nameList = array();
		while($row = $result->fetchRow()){
			array_push($nameList, $row['name']);
		}

		return $nameList;
	}
	/**
	 * @return []
	 */
	public function nextInvoiceIDs($userid, $year) {

		$nextInvoiceIds = array();
		
		// get free invoice ids
		$sql = 'SELECT cast(`rechnungsnummer` AS UNSIGNED) AS `rechnungsnummer`
				FROM `*PREFIX*pb_vk_freeinvoiceno`
				WHERE `userid` = ?
					AND `rechnungsnummer` != \'keine\'
					AND `rechnungsjahr` = ?
				ORDER BY `rechnungsnummer` ASC';

		$result = $this->execute($sql, array($userid, $year));

		while($row = $result->fetchRow()){
			settype($row['rechnungsnummer'], 'int');
			array_push($nextInvoiceIds, $row['rechnungsnummer']);
		}
		
		// get current max id +1
		$sql = 'SELECT MAX(CAST(`rechnungsnummer` AS UNSIGNED))+1 AS `nextInvoiceId`, count(`rechnungsnummer`) AS `count`
				FROM `' . $this->getTableName() . '`
				WHERE `userid` = ?
					AND `rechnungsnummer` != \'keine\'
					AND `rechnungsjahr` = ?';

		$result = $this->execute($sql, array($userid, $year));

		$row = $result->fetchRow();
		settype($row['nextInvoiceId'], 'int');
		settype($row['count'], 'int');
		if ( $row['nextInvoiceId'] ) {
			array_push($nextInvoiceIds, $row['nextInvoiceId']);
		} else if ($row['count'] === 0){
			array_push($nextInvoiceIds, 1);
		}

		return $nextInvoiceIds;
	}
	/**
	 * @return []
	 */
	public function rememberFreeInvoiceID($userid, $year, $invoiceid) {
		
		
		// get current max id
		$sql = 'SELECT max(`rechnungsnummer`) AS `maxInvoiceId`
				FROM `' . $this->getTableName() . '`
				WHERE `userid` = ?
					AND `rechnungsjahr` = ?';

		$result = $this->execute($sql, array($userid, $year));

		$row = $result->fetchRow();
		
		//only remember id if it is not the current maximum
		if ($row && isset($row['maxInvoiceId']) && $row['maxInvoiceId'] !== $invoiceid) {
		
			$sql = 'INSERT INTO `*PREFIX*pb_vk_freeinvoiceno`
					( `userid`, `rechnungsjahr`, `rechnungsnummer` )
					VALUES (?,?,?)';

			$this->execute($sql, array($userid, $year, $invoiceid));
			
		}

	}
	
	/**
	 * @return []
	 */
	public function forgetFreeInvoiceID($userid, $year, $invoiceid) {
		
		$sql = 'DELETE FROM `*PREFIX*pb_vk_freeinvoiceno`
				WHERE `userid` = ?
					AND `rechnungsjahr` = ?
					AND `rechnungsnummer` = ?';

		$this->execute($sql, array($userid, $year, $invoiceid));

	}
	
	/**
	 * @return []
	 */
	public function missingWertstellung($userid) {

		$sql = 'SELECT `' . $this->getTableName() .'`.*,
				SUM(`stueck`*`brutto`) AS `brutto_total`
			FROM `' . $this->getTableName() .'`
			JOIN `*PREFIX*pb_vk_positionen`
				ON `' . $this->getTableName() . '`.`id`=`*PREFIX*pb_vk_positionen`.`vk_id`
			WHERE `' . $this->getTableName() . '`.`wertstellung` IS NULL
				AND `' . $this->getTableName() . '`.`userid` = ?
			GROUP BY `' . $this->getTableName() . '`.`id`';

		$result = $this->execute($sql, array($userid));

		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new Verkauf();
			$entity->fromRow($row);
			array_push($entityList, $entity);
		}

		return $entityList;
	}
	/**
	 * @return []
	 */
	public function current($userid) {

		$start = date('Y-m-01');
		$d = new \DateTime( $start );
		$end = $d->format( 'Y-m-t' );

		$params = array($start, $end);

		$where = '
			(`' . $this->getTableName() . '`.`wertstellung` IS NULL
			OR `' . $this->getTableName() . '`.`wertstellung` BETWEEN ? AND ? )
		';

		return $this->overview($userid, $where, $params);

	}
	/**
	 * @return []
	 */
	public function overdue($userid) {

		$before = date('Y-m-01');

		$params = array($before);

		$where = '(
			`' . $this->getTableName() . '`.`wertstellung` < ?
			AND (
			`geliefert` < 1
			OR `' . $this->getTableName() . '`.`rechnungsnummer` IS NULL
			)
		)';

		return $this->overview($userid, $where, $params);
	}
	/**
	 * @return []
	 */
	public function searchByAccount($userid, $query) {

		$query = '%' . $query . '%';

		$params = array($query);
		
		$where = '`' . $this->getTableName() . '`.`account` LIKE ?';
		
		return $this->overview($userid, $where, $params);
	}
	/**
	 * @return []
	 */
	public function searchByName($userid, $query) {

		$query = '%' . $query . '%';

		$params = array($query);
		
		$where = '`' . $this->getTableName() . '`.`name` LIKE ?';
		
		return $this->overview($userid, $where, $params);
	}
	/**
	 * @return []
	 */
	private function overview($userid, $where, $params) {
		
		$sql = 'SELECT `' . $this->getTableName() .'`.`id`,
				`' . $this->getTableName() .'`.`userid`,
				`' . $this->getTableName() .'`.`wertstellung`,
				`' . $this->getTableName() .'`.`plattform`,
				`' . $this->getTableName() .'`.`account`,
				`' . $this->getTableName() .'`.`name`,
				`' . $this->getTableName() .'`.`zahlweise`,
				`' . $this->getTableName() .'`.`rechnungsjahr`,
				`' . $this->getTableName() .'`.`rechnungsnummer`,
				`' . $this->getTableName() .'`.`faultyreason`,
				AVG(`geliefert`) AS `geliefert`,
				SUM(`stueck`*`brutto`) AS `brutto`,
				SUM(`mwst`) AS `mwst`,
				SUM(`netto`) AS `netto`
			FROM `' . $this->getTableName() .'`
			JOIN `*PREFIX*pb_vk_positionen`
				ON `' . $this->getTableName() . '`.`id`=`*PREFIX*pb_vk_positionen`.`vk_id`
			WHERE `' . $this->getTableName() . '`.`userid` = ?
				AND '. $where .'
			GROUP BY `' . $this->getTableName() . '`.`id`
			ORDER BY `' . $this->getTableName() . '`.`id` DESC';

		$result = $this->execute($sql, array_merge( array($userid), $params) );
		
		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new Verkauf();
			$entity->fromRow($row);
			if ($entity->brutto != null) {
				settype( $entity->brutto, 'float' );
			}
			if ($entity->mwst != null) {
				settype( $entity->mwst, 'float' );
			}
			if ($entity->netto != null) {
				settype( $entity->netto, 'float' );
			}
			array_push($entityList, $entity);
		}

		return $entityList;
	}
	
	/**
	wertstellung,  brutto, mwst, netto, name, zahlweise (paypal, bar, konto)
	 * positionstyp (wenn alle positionen gleich, sonst '#gemischt' in rot) witd später analysiert?
	 * @return []
	 */
	public function report($start, $end, $userid){

		$sql = 'SELECT `' . $this->getTableName() . '`.`id`, `zahlweise`, `wertstellung`,
					SUM(`stueck`*`brutto`) AS `brutto`, `name`, `typ`, SUM(`mwst`) AS `mwst`,
					SUM(`netto`) AS `netto`, `rechnungsjahr`, `rechnungsnummer`, `faultyreason`
				FROM `' . $this->getTableName() .'`
				JOIN `*PREFIX*pb_vk_positionen`
					ON `' . $this->getTableName() . '`.`id`=`*PREFIX*pb_vk_positionen`.`vk_id`
				WHERE `userid` = ?
					AND `' . $this->getTableName() . '`.`wertstellung` BETWEEN ? AND ?
				GROUP BY `vk_id`
				ORDER BY `zahlweise`, `wertstellung`, `vk_id`';

		$result = $this->execute($sql,array($userid,$start,$end));

		$entityList = array();
		while($row = $result->fetchRow()){
			$entity = new Verkauf();
			$entity->fromRow($row);
			if ($entity->brutto != null) {
				settype($entity->brutto,'float');
			}
			if ($entity->mwst != null) {
				settype($entity->mwst,'float');
			}
			if ($entity->netto != null) {
				settype($entity->netto,'float');
			}
			array_push($entityList, $entity);
		}

		return $entityList;
	}


}
