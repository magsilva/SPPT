<?php

require_once(__DIR__ . '/Assignment.class.php');

/**
 * Grader for JFlap programs.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class CmdlineInputOutputAssignment extends Assignment
{
	private $input;

	private $output;

	public function setInput($input) {
		$this->input = $input;
	}

	public function getInput() {
		return $this->input;
	}

	public function setOutput($output) {
		$this->output = $output;
	}

	public function getOutput() {
		return $this->output;
	}

	public function loadData($data) {
		$this->setInput($data['input']);
		$this->setOutput($data['output']);
		$word = "";
		if (is_array($this->getInput())) {
			foreach ($this->getInput() as $input) {
				$word .= ' ' . $input;
			}
		} else {
			$word = ' ' . $this->getInput();
		}
		$word = trim($word);

		$this->setName($this->getName() . '. Palavra para reconhecimento: "' . $word . '"');
	}

	public function getSupportedFeatures() {
		$features = array();
                $features[] = 'SoftwareTesting_Driver_CommandLineInputOutput';
                return $features;
	}
}

?>
