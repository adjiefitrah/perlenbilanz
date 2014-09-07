<?php


namespace OCA\Perlenbilanz\Http;

use OCA\Perlenbilanz\AppFramework\Http\Response;

/**
 * A renderer for JSON calls
 */
class JSONResponse extends Response {

	private $data;
	private $options;


	public function __construct() {
		//parent::__construct();
		$this->data = array();
		$this->addHeader('X-Content-Type-Options', 'nosniff');
		$this->addHeader('Content-type', 'application/json');
	}


	/**
	 * Sets values in the data json array
	 * @param array $params an array with key => value structure which will be
	 *                      transformed to JSON
	 */
	public function setData($data, $encodeoptions = JSON_FORCE_OBJECT){
		$this->data = $data;
		$this->options = $encodeoptions;
	}


	/**
	 * Used to get the set parameters
	 * @return array the params
	 */
	public function getData(){
		return $this->data;
	}
	/**
	 * Used to get the json encode parameters
	 * @return int the params
	 */
	public function getEncodeOptions(){
		return $this->options;
	}

	/**
	 * Returns the rendered json
	 * @return string the rendered json
	 */
	public function render(){
		return json_encode($this->data,$this->options);
	}

}