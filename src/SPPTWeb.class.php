<?php


require_once(__DIR__ . '/SPPT.class.php');
require_once(__DIR__ . '/Submission.class.php');

/**
 * Web controller for Small Python Program Tester.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class SPPTWeb
{
	/**
         * Name of form element that stores student's code.
         */
        const RA_INPUT = 'ra';

	/**
         * Name of form element that stores the filename.
         */
        const FILENAME_INPUT = 'file';

	/**
	 * Configuration of $maxfilesize (maximum byte size of uploaded file).
	 */
	private $maxFileSize= 1024 * 1024 * 2; 

	/**
	 * Supported file extensions.
	 */
	private $allowedFileExtensions = array();

	/**
	 * Error messages regarding file upload.
	 */
	private $uploadErrorMessages = array();

	private $baseUrl = NULL;

	private $baseDir = __DIR__;

	private $baseUploadDir = __DIR__ . '/uploads';

	public function __construct() {
		$this->setDefaultAllowedFileExtensions();
		$this->setDefaultErrorMessages();
	}

	private function setDefaultAllowedFileExtensions() {
		$this->allowedFileExtensions[] = 'py';
		$this->allowedFileExtensions[] = 'jff';
		$this->allowedFileExtensions[] = 'txt';
	}

	private function setDefaultErrorMessages() {
		$this->uploadErrorMessages[0] = 'Não houve erro';
		$this->uploadErrorMessages[1] = 'O arquivo no upload é maior do que o limite do PHP';
		$this->uploadErrorMessages[2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
		$this->uploadErrorMessages[3] = 'O upload do arquivo foi feito parcialmente';
		$this->uploadErrorMessages[4] = 'Não foi feito o upload do arquivo';
	}

	public function setBaseUrl($url) {
		$this->baseUrl = $url;
	}

	public function getBaseUrl() {
		return $this->baseUrl;
	}

	public function setBaseDir($baseDir) {
		$this->baseDir = $baseDir;
	}

	public function getBaseDir() {
		return $this->baseDir;
	}

	public function setBaseUploadDir($baseUploadDir) {
		$this->baseUploadDir = $baseUploadDir;
	}

	public function getBaseUploadDir() {
		return $this->baseUploadDir;
	}

	/**
	 * Check if there is something to process from the HTTP request.
	 *
	 * @return Boolean True if something was sent in the form, False otherwise.
	 */
	public function hasSomethingToProcess($request, $upload) {
		if (isset($request['submit'])) {
			return True;
		}
		return False;
	}


	/**
	 * Process HTTP request.
	 *
	 * @return Array with results.
	 */
	public function processUploadRequest($request, $upload) {
		if (! $this->hasSomethingToProcess($request, $upload)) {
			throw new Exception('Nothing to process');
		}

		// Verifica se foi informado um RA válido
		if (! isset($request[SPPTWeb::RA_INPUT])) {
			throw new Exception('Student\'s code has not been informed');
		}
	
		if (! is_numeric($request[SPPTWeb::RA_INPUT])) {
			throw new Exception('Invalid student\'s code: ' . $request[SPPTWeb::RA_INPUT]);
		}

		// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
		if ($upload[SPPTWeb::FILENAME_INPUT]['error'] != 0) {
			throw new Exception('Upload failed: ' . $this->uploadErrorMessages[$uploadS[SPPTWeb::FILENAME_INPUT]['error']]);
		}

		$ext = pathinfo($upload[SPPTWeb::FILENAME_INPUT]['name'], PATHINFO_EXTENSION);
		if (! in_array($ext, $this->allowedFileExtensions)) {
			throw new Exception('Forbidden filename extension: ' . $ext);
		}
	 
		if ($upload[SPPTWeb::FILENAME_INPUT]['size'] > $this->maxFileSize) {
			throw new Exception('The file size is bigger than the maximum acceptable value. Please send a smaller file.');
		}

		$sppt = new SPPT();
		$sppt->setDatadir($this->baseUploadDir);
		$submission = new Submission($sppt->getDatadir(), $request[SPPTWeb::RA_INPUT]);
		move_uploaded_file($upload[SPPTWeb::FILENAME_INPUT]['tmp_name'], $submission->getWorkingDir() . '/' . $upload[SPPTWeb::FILENAME_INPUT]['name']);
		$submission->setFile($upload[SPPTWeb::FILENAME_INPUT]['name']);
		$result = $sppt->assess($submission);

		return $result;
	}
}

?>
