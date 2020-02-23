<?php

/**
 * Final assessment results. It can have several partial assessment results
 * (for instance, one partial result for each test set).
 */
class Assessment {
	/**
	 * Identifier for this assessment.
	 */
	private $name;

	/**
	 * Number of verifications or validations that passed.
	 */	
	private $successes;

	/**
	 * Number of verifications or validations that failed or have an error.
	 */	
	private $errors;

	/**
	 * Messages for each error (array of string).
	 */
	private $errorMessages;

	/**
	 * Associative array of coverages with respect to some criteria. The key for
	 * each element is the coverage criterion.
	 */
	private $coverage;

	/**
	 * Array of Assessment for partial results. 
	 */
	private $partialResults;

	public function __construct($name) {
		$this->name = $name;
		$this->successes = 0;
		$this->errors = 0;
		$this->errorMessages = array();
		$this->coverage = array();
		$this->partialResults = array();
	}

	public function getName() {
		return $this->name;
	}

	public function getSuccesses() {
		return $this->successes;
	}

	public function addSuccess() {
		$this->successes++;
	}

	public function getErrors() {
		return $this->errors;
	}

	public function addError() {
		$this->errors++;
	}

	public function hasFailed() {
		return ($this->errors > 0);
	}

	public function addErrorMessage($errorMessage) {
		$this->errorMessages[] = $errorMessage;
	}

	public function getErrorMessages() {
		return $this->errorMessages;
	}

	public function setCoverage($criterion, $value) {
		$this->coverage[$criterion] = $value;
	}

	public function getCoverage() {
		return $this->coverage;
	}

	public function addPartialResult($assessment) {
		$this->partialResults[] = $assessment;
		$this->successes += $assessment->successes;
		$this->errors += $assessment->errors;
		$this->errorMessages += $assessment->errorMessages;
	}

	public function getPartialResults() {
		return $this->partialResults;
	}
}

?>
