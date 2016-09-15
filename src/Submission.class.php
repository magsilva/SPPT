<?php

class Submission {

	private $workingDir;

	private $file; // Submitted file

	private $assessment;

	public $stmtCoverage;

	public $branchCoverage;

	public function __construct($workingDir, $ra, $timestamp = null) {
		if ($timestamp == null) {
			$timestamp = time();
		}
		$this->workingDir = $workingDir . '/' . $ra . '/' . $timestamp;
		@mkdir($this->workingDir, 0700, TRUE);
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

	public function setAssessment($assessment) {
		$this->assessment = $assessment;
	}

	public function getAssessment() {
		return $this->assessment;
	}

}

?>
