<?php

require_once(__DIR__ . '/CmdlineInputOutputAssignment.class.php');

/**
 * Grader for JFlap programs.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class CmdlineInputOutputWithStdoutAssignment extends CmdlineInputOutputAssignment
{
	private $stdout;

	public function setStdout($stdout) {
		$this->stdout = $stdout;
	}

	public function getStdout() {
		return $this->stdout;
	}

	public function loadData($data) {
		$this->setInput($data['input']);
		$this->setOutput($data['output']);
		$this->setStdout($data['stdout']);
		$this->setName($this->getName() . '. Palavra para processamento: "' . $this->getInput() . '"');
	}

	public function getSupportedFeatures() {
		$features = array();
                $features[] = 'SoftwareTesting_Driver_CommandLineInputOutputWithStdout';
                return $features;
	}
}

?>
