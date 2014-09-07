<?php

namespace OCA\Perlenbilanz\DependencyInjection;

use OCA\Perlenbilanz\AppFramework\Http\Http;
use OCA\Perlenbilanz\AppFramework\Http\Request;
use OCA\Perlenbilanz\AppFramework\Http\Dispatcher;
use OCA\Perlenbilanz\AppFramework\Core\API;
use OCA\Perlenbilanz\AppFramework\Middleware\MiddlewareDispatcher;
use OCA\Perlenbilanz\AppFramework\Middleware\Http\HttpMiddleware;
use OCA\Perlenbilanz\AppFramework\Middleware\Security\SecurityMiddleware;

// in OC6 pimple is already loaded
if(!class_exists('Pimple')) {
	require_once __DIR__ . '/../3rdparty/Pimple/Pimple.php';
}


/**
 * This class extends Pimple (http://pimple.sensiolabs.org/) for reusability
 * To use this class, extend your own container from this. Should you require it
 * you can overwrite the dependencies with your own classes by simply redefining
 * a dependency
 */
class DIContainer extends \Pimple {

	/**
	 * Put your class dependencies in here
	 */
	public function __construct(){

		$this['AppName'] = 'perlenbilanz';

		$this['API'] = $this->share(function($c){
			return new API($c['AppName']);
		});

		/**
		 * Http
		 */
		$this['Request'] = $this->share(function($c) {
			$params = json_decode(file_get_contents('php://input'), true);
			$params = is_array($params) ? $params: array();

			return new Request(
				array(
					'get' => $_GET,
					'post' => $_POST,
					'files' => $_FILES,
					'server' => $_SERVER,
					'env' => $_ENV,
					'session' => $_SESSION,
					'cookies' => $_COOKIE,
					'method' => (isset($_SERVER) && isset($_SERVER['REQUEST_METHOD']))
							? $_SERVER['REQUEST_METHOD']
							: null,
					'params' => $params,
					'urlParams' => $c['urlParams']
				)
			);
		});

		$this['Protocol'] = $this->share(function(){
			if(isset($_SERVER['SERVER_PROTOCOL'])) {
				return new Http($_SERVER, $_SERVER['SERVER_PROTOCOL']);
			} else {
				return new Http($_SERVER);
			}
		});

		$this['Dispatcher'] = $this->share(function($c) {
			return new Dispatcher($c['Protocol'], $c['MiddlewareDispatcher']);
		});

		/**
		 * Middleware
		 */
		$this['SecurityMiddleware'] = $this->share(function($c){
			return new SecurityMiddleware($c['API'], $c['Request']);
		});

		$this['HttpMiddleware'] = $this->share(function($c){
			return new HttpMiddleware($c['API'], $c['Request']);
		});

		$this['MiddlewareDispatcher'] = $this->share(function($c){
			$dispatcher = new MiddlewareDispatcher();
			$dispatcher->registerMiddleware($c['HttpMiddleware']);
			$dispatcher->registerMiddleware($c['SecurityMiddleware']);
			return $dispatcher;
		});

		require_once __DIR__ . '/diconfig.php';
	}

}
