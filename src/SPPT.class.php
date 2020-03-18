<?php

require_once(__DIR__ . '/Assessment.class.php');

/**
 * SimPle Program Tester.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class SPPT
{
	/**
	 * Configuration of $datadir (directory where files will be saved to).
	 */
	private $datadir;

	public const ID_PATTERN = '/^([a-z0-9-_:])+$/i';

	public const USER_ID_PATTERN = '/^a?[0-9]+$/i';

	public function setDatadir($datadir) {
		if (! file_exists($datadir)) {
			$result = mkdir($datadir, 0700, true);
			if ($result === False) {
				throw new Exception('Cannot create directory to save files: ' . $datadir);
			}
			$this->datadir = $datadir;
		} else {
			if (! is_dir($datadir)) {
				throw new Exception('Cannot use directory to save files (actually it is an existing file!): ' . $datadir);
			} else {
				$this->datadir = $datadir;
			}
		}
	}

	public function getDatadir() {
		return $this->datadir;
	}


	public function assess($submission, $assignment) {
		$assessments = array();
		$availableGraders = array();
		$validGraders = array();
		
		foreach (glob(__DIR__ . '/*Grader.class.php', GLOB_NOSORT) as $filename) {
			require_once($filename);
			$basename = pathinfo($filename, PATHINFO_BASENAME);
			$classname = str_replace('.class.php', '', $basename);
			$grader = new $classname();
			$availableGraders[] = $grader;
		}

		foreach ($availableGraders as $grader) {
			if ($grader->canEvaluate($submission, $assignment)) {
				$validGraders[] = $grader;
			}
		}
		if (count($validGraders) == 0) {
			throw new Exception('Could not provide a grader for this submission');
		}

		foreach ($validGraders as $grader) {
			$assessments[$grader->getOutputFormat()] = $grader->evaluate($submission, $assignment);
		}

		$submission->setAssessments($assessments);
		return $assessments;
	}
}

?>
