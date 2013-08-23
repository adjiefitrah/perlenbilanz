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

namespace OCA\Perlenbilanz\DependencyInjection;

use OCA\AppFramework\DependencyInjection\DIContainer as BaseContainer;

use OCA\Perlenbilanz\Controller\PageController;
use OCA\Perlenbilanz\Controller\NotesController;
use OCA\Perlenbilanz\Controller\EinkaufController;
use OCA\Perlenbilanz\Controller\EinkaufPositionController;
use OCA\Perlenbilanz\Controller\VerkaufController;
use OCA\Perlenbilanz\Controller\VerkaufPositionController;
use OCA\Perlenbilanz\Controller\ReportController;
use OCA\Perlenbilanz\Db\EinkaufMapper;
use OCA\Perlenbilanz\Db\EinkaufPositionMapper;
use OCA\Perlenbilanz\Db\VerkaufMapper;
use OCA\Perlenbilanz\Db\VerkaufPositionMapper;


require_once __DIR__ . '/../3rdparty/PHPExcel.php';

class DIContainer extends BaseContainer {


	/**
	 * Define your dependencies in here
	 */
	public function __construct(){
		// tell parent container about the app name
		parent::__construct('perlenbilanz');


		/**
		 * Delete the following twig config to use ownClouds default templates
		 */
		// use this to specify the template directory
		$this['TwigTemplateDirectory'] = __DIR__ . '/../templates';

		// if you want to cache the template directory in yourapp/cache
		// uncomment this line. Remember to give your webserver access rights
		// to the cache folder 
		// $this['TwigTemplateCacheDirectory'] = __DIR__ . '/../cache';		

		/** 
		 * CONTROLLERS
		 */
        $this['PageController'] = $this->share(function($c){
            return new PageController($c['API'], $c['Request']);
        });

		$this['EinkaufController'] = $this->share(function($c){
			return new EinkaufController($c['API'], $c['Request'], $c['EinkaufMapper']);
		});
		$this['EinkaufPositionController'] = $this->share(function($c){
			return new EinkaufPositionController($c['API'], $c['Request'], $c['EinkaufPositionMapper'], $c['EinkaufMapper']);
		});

		$this['VerkaufController'] = $this->share(function($c){
			return new VerkaufController($c['API'], $c['Request'], $c['VerkaufMapper'], $c['VerkaufPositionMapper']);
		});
		$this['VerkaufPositionController'] = $this->share(function($c){
			return new VerkaufPositionController($c['API'], $c['Request'], $c['VerkaufPositionMapper'], $c['VerkaufMapper']);
		});

		$this['ReportController'] = $this->share(function($c){
			return new ReportController($c['API'], $c['Request'], $c['EinkaufMapper'], $c['VerkaufMapper']);
		});
		$this['NotesController'] = $this->share(function($c){
			return new NotesController($c['API'], $c['Request']);
		});
		/*
		$this['SettingsController'] = $this->share(function($c){
			return new SettingsController($c['API'], $c['Request']);
		});
		*/

		/**
		 * MAPPERS
		 */
		$this['EinkaufMapper'] = $this->share(function($c){
			return new EinkaufMapper($c['API']);
		});

		$this['EinkaufPositionMapper'] = $this->share(function($c){
			return new EinkaufPositionMapper($c['API']);
		});

		$this['VerkaufMapper'] = $this->share(function($c){
			return new VerkaufMapper($c['API']);
		});

		$this['VerkaufPositionMapper'] = $this->share(function($c){
			return new VerkaufPositionMapper($c['API']);
		});

	}
}

