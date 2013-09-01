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

namespace OCA\Perlenbilanz;

use \OCA\AppFramework\App;

use \OCA\Perlenbilanz\DependencyInjection\DIContainer;


/*************************
 * Define your routes here
 ************************/

/**
 * Normal Routes
 */
$this->create('perlenbilanz_menu', '/')->get()->action(
	function($params){
		App::main('PageController', 'index', $params, new DIContainer());
	}
);


/**
 * Bericht
 */
$this->create('perlenbilanz_bericht', '/report')->get()->action(
	function($params){
		App::main('ReportController', 'renderReport', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_invoice', '/ajax/render/invoice')->post()->action(
	function($params){
		App::main('InvoiceController', 'renderInvoice', $params, new DIContainer());
	}
);

/**
 * Notes
 */
$this->create('perlenbilanz_notes_get', '/ajax/notes')->get()->action(
	function($params){
		App::main('NotesController', 'getNotes', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_notes_save', '/ajax/notes')->post()->action(
	function($params){
		App::main('NotesController', 'saveNotes', $params, new DIContainer());
	}
);

/**
 * Ajax Routes
 */
$this->create('perlenbilanz_ajax_einkauf_list', '/ajax/einkauf')->get()->action(
	function($params){
		App::main('EinkaufController', 'listEinkaeufe', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_einkauf_get', '/ajax/einkauf/{id}')->get()->action(
	function($params){
		App::main('EinkaufController', 'getEinkauf', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_einkauf_post', '/ajax/einkauf')->post()->action(
	function($params){
		App::main('EinkaufController', 'postEinkauf', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_einkauf_put', '/ajax/einkauf/{id}')->post()->action(
	function($params){
		App::main('EinkaufController', 'putEinkauf', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_einkauf_position_list', '/ajax/einkaufposition')->get()->action(
	function($params){
		App::main('EinkaufPositionController', 'listPositionen', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_einkauf_position_post', '/ajax/einkaufposition')->post()->action(
	function($params){
		App::main('EinkaufPositionController', 'postPosition', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_einkauf_position_put', '/ajax/einkaufposition/{id}')->post()->action(
	function($params){
		App::main('EinkaufPositionController', 'putPosition', $params, new DIContainer());
	}
);

/**
 * Verkauf
 */

$this->create('perlenbilanz_ajax_verkauf_list', '/ajax/verkauf')->get()->action(
	function($params){
		App::main('VerkaufController', 'listVerkaeufe', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_verkauf_get', '/ajax/verkauf/{id}')->get()->action(
	function($params){
		App::main('VerkaufController', 'getVerkauf', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_verkauf_post', '/ajax/verkauf')->post()->action(
	function($params){
		App::main('VerkaufController', 'postVerkauf', $params, new DIContainer()); //TODO rename to createVerkauf
	}
);
$this->create('perlenbilanz_ajax_verkauf_put', '/ajax/verkauf/{id}')->post()->action(
	function($params){
		App::main('VerkaufController', 'putVerkauf', $params, new DIContainer()); //TODO rename to updateVerkauf, use PUT?
	}
);
$this->create('perlenbilanz_ajax_verkauf_invoice_delete', '/ajax/verkauf/{id}/invoice')->post()->action(
	function($params){
		App::main('InvoiceController', 'deleteInvoice', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_verkauf_position_list', '/ajax/verkaufposition')->get()->action(
	function($params){
		App::main('VerkaufPositionController', 'listPositionen', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_verkauf_position_post', '/ajax/verkaufposition')->post()->action(
	function($params){
		App::main('VerkaufPositionController', 'postPosition', $params, new DIContainer());
	}
);
$this->create('perlenbilanz_ajax_verkauf_position_put', '/ajax/verkaufposition/{id}')->post()->action(
	function($params){
		App::main('VerkaufPositionController', 'putPosition', $params, new DIContainer());
	}
);
