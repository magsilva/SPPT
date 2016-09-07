<?php

class Submission {

	private $workingDir;

	private $file;

	public $results;

	public $resultsCsv;

	public function __construct($workingDir, $ra) {
		$this->workingDir = $workingDir . '/' . $ra . '/' . time();
		mkdir($this->workingDir, 0700, TRUE);
		$this->results = array();
	}

	public function getWorkingDir() {
		return $this->workingDir;
	}

	public function setFile($file) {
		if (is_file($this->workingDir . '/' . basename($file))) {
			$this->file = $this->workingDir . '/' . basename($file);
		} else {
			throw new Exception('Invalid file: ' . $file);
		}
	}

	public function getFile() {
		return $this->file;
	}

	public function setResultsCsv($file)
        {
                if (is_string($file)) {
                        $file = fopen($this->workingDir . '/'. basename($file), 'a');
                        if ($file !== FALSE) {
                                $this->resultsCsv = $result;
                        }
                } else if (is_resource($file) && (get_resource_type($file) == 'file' || get_resource_type($file) == 'stream')) {
                        $this->resultsCsvt = $file;
                }
        }

        public function getResultsCsv()
        {
                return stream_get_meta_data($this->resultsCsv)['uri'];
        }

	public function addResult($data)
	{
		
	}


}

?>
