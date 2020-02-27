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
		return $features;
	}

	public function getOutputFormat() {
		return 'XUnit XML';
	}

	public function canEvaluate($submission, $assignment) {
                $ext = pathinfo($submission->getFile(), PATHINFO_EXTENSION);
                if (strcasecmp($ext, 'jff') == 0 || strcasecmp($ext, 'txt') == 0) {
			if (in_array('SoftwareTesting_Driver_CommandLineInputOutput', $assignment->getSupportedFeatures())) {
				return True;
			}
		}
		return False;
	}

	public function evaluate($submission, $assignment) {
		$assessment = new Assessment($assignment->getName());

		if (in_array('SoftwareTesting_Driver_CommandLineInputOutput', $assignment->getSupportedFeatures())) {
			$comando = 'PYTHONPATH=' . $this->flaRunnerPath;
			$comando .= ' ' . $this->python3Path;
			$comando .= ' ' . $this->flaRunnerPath . '/fla/main.py';
	                $comando .= ' ' . escapeshellarg($submission->getFile());
       		        $comando .= ' ' . escapeshellarg($assignment->getInput());
			$cwd = getcwd();
        	        chdir($submission->getWorkingDir());
                	exec($comando, $output, $retval);
			chdir($cwd);

			if (($retval == 0 && $assignment->getOutput() == True) || ($retval != 0 && $assignment->getOutput() == False)) {
				$assessment->addSuccess();
			} else {
				$assessment->addError();
				$assessment->addErrorMessage($output);
			}
		}

		return $assessment;
	}
}

?>
