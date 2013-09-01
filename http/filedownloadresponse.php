<?php


namespace OCA\Perlenbilanz\Http;

use OCA\AppFramework\Http\DownloadResponse;

/**
 * A renderer for JSON calls
 */
class FileDownloadResponse extends DownloadResponse {

	protected $content;
	protected $filename;
	protected $contentType;

	/**
	 * Creates a response that prompts the user to download the file
	 * @param string $filename the name that the downloaded file should have
	 * @param string $contentType the mimetype that the downloaded file should have
	 */
	public function __construct($filename, $contentType) {
		$this->filename = $filename;
		$this->contentType = $contentType;

		$this->addHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
		$this->addHeader('Content-Type', $contentType);
	}
	
	/**
	 * Returns the rendered json
	 * @return string the rendered json
	 */
	public function render(){
		\OC\Files\Filesystem::readfile($this->filename);
	}

}