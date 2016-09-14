<?php

abstract class AssessmentResult {
	
	private $name;

	private $failed;

	private $errors;

	public function __construct($name, $failed) {
		$this->name = $name;
		$this->failed = $failed;
		$this->errors = array();
	}

	public function getName() {
		return $this->name;
	}

	public function hasFailed() {
		return $this->failed;
	}

	public function addError($error) {
		$this->errors[] = $error;
	}

	public function getErrors() {
		return $this->errors;
	}
}

?>
