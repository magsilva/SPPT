<?php

require_once(__DIR__ . '/Assessment.class.php');

/**
 * Grader for JFlap programs.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class JFlapGrader
{
	private $flaRunnerPath = '/opt/fla-runner';

	private $python3Path = 'python3';

	public function getName() {
		return 'JFlap grader';
	}

	public function getFeatures() {
		$features = array();
		$features[] = 'SoftwareTesting_Driver_CommandLineInputOutput';
		$features[] = 'SoftwareTesting_Driver_CommandLineInputOutputWithStdout';
		return $features;
	}

	public function getOutputFormat() {
		return 'XUnit XML';
	}

	public function canEvaluate($submission, $assignment) {
                $ext = pathinfo($submission->getFile(), PATHINFO_EXTENSION);
		if (strcasecmp($ext, 'jff') == 0 || strcasecmp($ext, 'txt') == 0) {
			foreach ($this->getFeatures() as $supportedFeature) {
				if (in_array($supportedFeature, $assignment->getSupportedFeatures())) {
					return True;
				}
			}
		}
		return False;
	}

	public function evaluate($submission, $assignment) {
		$assessment = new Assessment($assignment->getName());

		if (
			in_array('SoftwareTesting_Driver_CommandLineInputOutput', $assignment->getSupportedFeatures()) ||
			in_array('SoftwareTesting_Driver_CommandLineInputOutputWithStdout', $assignment->getSupportedFeatures())
		) {
			$comando = 'PYTHONPATH=' . $this->flaRunnerPath;
			$comando .= ' ' . $this->python3Path;
			$comando .= ' ' . $this->flaRunnerPath . '/fla/main.py';
	                $comando .= ' ' . escapeshellarg($submission->getFile());
       		        $comando .= ' ' . escapeshellarg($assignment->getInput());
			$cwd = getcwd();
        	        chdir($submission->getWorkingDir());
			exec($comando, $output, $retval);
			chdir($cwd);
			$output = implode(" ", $output);

			if (($retval == 0 && $assignment->getOutput() == True) || ($retval != 0 && $assignment->getOutput() == False)) {
				if (in_array('SoftwareTesting_Driver_CommandLineInputOutputWithStdout', $assignment->getSupportedFeatures())) {
					if ($output == $assignment->getStdout()) {
						$assessment->addSuccess();
					} else {
						$assessment->addError();
						$assessment->addErrorMessage($output);
					}
				} else {
					$assessment->addSuccess();
				}
			} else {
				$assessment->addError();
				$assessment->addErrorMessage($output);
			}
		}

		return $assessment;
	}
}

?>
