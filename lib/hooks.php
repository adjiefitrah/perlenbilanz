<?php

namespace OCA\Perlenbilanz;

/**
 * 
 * @author Jörn Dreyer <jfd@butonic.de>
 */
class Hooks {

	/**
	 * classname which used for hooks handling
	 * used as signalclass in OC_Hooks::emit()
	 */
	const CLASSNAME = 'Hooks';

	/**
	 * handle for indexing file
	 *
	 * @param string $path
	 */
	const handle_post_write = 'renderInvoice';

	/**
	 * handle for removing file
	 *
	 * @param string $path
	 */
	const handle_delete = 'deleteFile';

	static function isPerlenbilanzInvoice($path) {
		return preg_match('/^\/Perlenbilanz\/Rechnungen\/Rechnung .*\.html/', $path);
	}
	
	/**
	 * handle file writes (render pdf from html)
	 * 
	 * @author Jörn Dreyer <jfd@butonic.de>
	 * 
	 * @param $param array from postWriteFile-Hook
	 */
	public static function renderInvoice(array $param) {
		
		if (self::isPerlenbilanzInvoice($param['path'])) {
			
			$html = \OC\Files\Filesystem::file_get_contents($param['path']);
			
			require_once __DIR__ . '/../3rdparty/MPDF56/mpdf.php';

			$mpdf=new \mPDF('utf-8', 'A4');

			$mpdf->WriteHTML($html);
			$pdf = $mpdf->Output(null, 'S');
			
			$path = substr($param['path'], 1, -4).'pdf';
			\OC\Files\Filesystem::file_put_contents($path, $pdf);
		
		}

	}

}
