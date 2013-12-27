<?php

namespace OCA\Perlenbilanz\Http;

use \OC_Template;

/**
 * This class provides the templates for owncloud.
 */
class Template extends \OC_Template {
	private $vars; // Vars
	private $template; // The path to the template
	private $l10n;

	/**
	 * @brief Constructor
	 * @param string $app app providing the template
	 * @param string $file name of the template file (without suffix)
	 * @param string $renderas = ""; produce a full page
	 * @return OC_Template object
	 *
	 * This function creates an OC_Template object.
	 *
	 * If $renderas is set, OC_Template will try to produce a full page in the
	 * according layout. For now, renderas can be set to "guest", "user" or
	 * "admin".
	 */
	public function __construct( $name ) {
		// Set the private data
		$this->vars = array();
		$this->vars['requesttoken'] = \OC_Util::callRegister();
		$this->l10n = \OC_L10N::get('perlenbilanz');
		
		$this->template = \OCP\Files::tmpFile('pb.tmpl');
		$this->findTemplate($name);
	}

	/**
	 * @brief find the template with the given name
	 * @param string $name of the template file (without suffix)
	 *
	 * Will select the template file for the selected theme and formfactor.
	 * Checking all the possible locations.
	 */
	protected function findTemplate($path)
	{
		// Check if the app is in the app folder or in the root
		if( \OC\Files\Filesystem::is_file($path) ) {
			//$this->template = \OC\Files\Filesystem::file_get_contents($path);
			
			$sh = \OC\Files\Filesystem::fopen($path, 'r');
			$th = fopen($this->template, 'w');
			\OCP\Files::streamCopy($sh, $th);
			fclose($sh);
			fclose($th);
			
		}else{
			echo('template not found: template:'.$path
				.' webroot:'.OC::$WEBROOT.' serverroot:'.OC::$SERVERROOT);
			die();

		}
	}

	/**
	 * @brief Assign variables
	 * @param string $key key
	 * @param string $value value
	 * @return bool
	 *
	 * This function assigns a variable. It can be accessed via $_[$key] in
	 * the template.
	 *
	 * If the key existed before, it will be overwritten
	 */
	public function assign( $key, $value) {
		$this->vars[$key] = $value;
		return true;
	}

	/**
	 * @brief Appends a variable
	 * @param string $key key
	 * @param string $value value
	 * @return bool
	 *
	 * This function assigns a variable in an array context. If the key already
	 * exists, the value will be appended. It can be accessed via
	 * $_[$key][$position] in the template.
	 */
	public function append( $key, $value ) {
		if( array_key_exists( $key, $this->vars )) {
			$this->vars[$key][] = $value;
		}
		else{
			$this->vars[$key] = array( $value );
		}
	}

	/**
	 * @brief Proceeds the template
	 * @return bool
	 *
	 * This function proceeds the template. If $this->renderas is set, it
	 * will produce a full page.
	 */
	public function fetchPage() {
		// Register the variables
		$_ = $this->vars;
		$l = $this->l10n;

		// Execute the template
		ob_start();
		//include 'data://text/plain;,'. urlencode($this->template); // <-- we have to use include because we pass $_!
		require $this->template; // <-- we have to use include because we pass $_!
		$data = ob_get_contents();
		@ob_end_clean();

		// return the data
		return $data;
	}

}
