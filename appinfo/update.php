<?php

// select id, vk_id, stueck, brutto as oldbrutto, round(brutto/stueck,2) as newbrutto, stueck*brutto as bruttoSum, mwst, mwst_prozent, netto, round(mwst+netto, 2) from oc_pb_vk_positionen where stueck*brutto != round(netto+mwst,2);

// we could just do this in the db but we wan't to write a log of what we updated
$stmt = \OC_DB::prepare('SELECT `id`, `stueck`, `brutto`'
						.' FROM `*PREFIX*pb_vk_positionen`'
						.' WHERE `stueck`*`brutto` != round(`netto`+`mwst`, 2)');

if (\OC_DB::isError($stmt)) {
	$msg = \OC_DB::getErrorMessage($stmt);
	throw new DatabaseException($msg);
}
$result = $stmt->execute();

if (\OC_DB::isError($result)) {
	$msg = \OC_DB::getErrorMessage($result);
	throw new DatabaseException($msg);
}

while($row = $result->fetchRow()) {
	$newBrutto = round($row['brutto']/$row['stueck'], 2);
	$formatted = number_format($newBrutto, 2, '.', ',');
	\OCP\Util::writeLog('perlenbilanz', 'correcting sale position '.$row['id'].': setting brutto from '.$row['brutto'].' to '.$formatted, \OCP\Util::WARN);
	$updateStmt = \OC_DB::prepare('UPDATE `*PREFIX*pb_vk_positionen`'
								.' SET brutto = round(brutto/stueck, 2)'
								.' WHERE id = ?');
	
	if (\OC_DB::isError($updateStmt)) {
		$msg = \OC_DB::getErrorMessage($updateStmt);
		throw new DatabaseException($msg);
	}
	
	$updateResult = $updateStmt->execute($row['id']);

	if (\OC_DB::isError($updateResult)) {
		$msg = \OC_DB::getErrorMessage($updateResult);
		throw new DatabaseException($msg);
	}
}

