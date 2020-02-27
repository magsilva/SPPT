<?php

require_once(__DIR__ . '/Assignment.class.php');

/**
 * Directory and factory for assingments.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class AssignmentDirectory
{
	private $baseDir;

	public function getBaseDir() {
		return $this->baseDir;
	}

	public function setBaseDir($baseDir) {
		$this->baseDir = $baseDir;
	}

	public function getBundles() {
		$bundles = array();
		foreach (glob($this->baseDir . '/*.json') as $filename) {
			$fileContents = file_get_contents($filename);
			$bundleData = json_decode($fileContents, True);
			$bundleId = pathinfo($filename, PATHINFO_FILENAME);
			$bundleData['id'] = $bundleId;
			$bundles[] = $bundleData;
		}
		return $bundles;
	}

	public function getAssignmentsFor($bundleId) {
		$assignments = array();

		foreach (glob($this->baseDir . '/' . $bundleId . '/*.json') as $filename) {
			$fileContents = file_get_contents($filename);
			$assignmentData = json_decode($fileContents, True);
			$assignmentId = pathinfo($filename, PATHINFO_FILENAME);
			$assignmentData['id'] = $assignmentId;
			$assignment = new Assignment();
			$assignment->setId($assignmentData['id']);
			$assignment->setName($assignmentData['name']);
			$assignment->setDescription($assignmentData['description']);
			$assignments[] = $assignment;
                }
		return $assignments;
	}


	public function getSpecificAssignmentsFor($bundleId, $assignmentId) {
		$assignments = array();

		foreach (glob($this->baseDir . '/' . $bundleId . '/*.json') as $filename) {
			$fileContents = file_get_contents($filename);
			$assignmentData = json_decode($fileContents, True);
			$currentAssignmentId = pathinfo($filename, PATHINFO_FILENAME);
			if ($currentAssignmentId == $assignmentId) {
				$assignmentData['id'] = $assignmentId;
				foreach ($assignmentData['evaluationData'] as $evaluationData) {
					$assignmentClassname = $evaluationData['type'] . 'Assignment';
					if (file_exists(__DIR__ . '/' . $assignmentClassname . '.class.php')) {
						require_once(__DIR__ . '/' . $assignmentClassname . '.class.php');
						foreach ($evaluationData['data'] as $singleEvaluationData) {
							$assignment = new $assignmentClassname();
							$assignment->setId($assignmentData['id']);
							$assignment->setName($assignmentData['name']);
							$assignment->setDescription($assignmentData['description']);
							$assignment->loadData($singleEvaluationData);
							$assignments[] = $assignment;
						}
					}
				}
			}
                }
		return $assignments;
	}
}

?>
