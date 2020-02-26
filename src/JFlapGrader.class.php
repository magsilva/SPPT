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

	public function canEvaluate($submission) {
                $ext = pathinfo($submission->getFile(), PATHINFO_EXTENSION);
                if (strcasecmp($ext, 'jff') == 0 || strcasecmp($ext, 'txt') == 0) {
			return True;
		}
		return False;
	}

	public function evaluate($submission) {
		$assessment = new Assessment("Formal model assessment by testing input words");

		$comando = 'PYTHONPATH=' . $this->flaRunnerPath;
		$comando .= ' ' . $this->python3Path;
		$comando .= ' ' . $this->flaRunnerPath . '/fla/main.py';
                $comando .= ' ' . escapeshellarg($submission->getFile());
       	        $comando .= ' ' . escapeshellarg('101');
		$cwd = getcwd();
                chdir($submission->getWorkingDir());
                exec($comando, $output, $retval);
		chdir($cwd);

		$partialAssessment = new Assessment('101');
		if ($retval == 0) {
			$partialAssessment->addSuccess();
		} else {
			$partialAssessment->addError();
			$partialAssessment->addErrorMessage($output);
		}
		$assessment->addPartialResult($partialAssessment);

		return $assessment;
	}
}

?>
