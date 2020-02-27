<?php

require_once(__DIR__ . '/Grader.interface.php');

/**
 * Small Python Program Tester.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class PythonGrader implements Grader
{
	private $nosecmd = '/usr/bin/nosetests';

	/**
	 * Blacklisted statements.
	 */
	private $blacklist = array();

        /**
         * Enable syntax and semantic verification (compiler).
         */
        private $compiler = TRUE;

	/**
	 * Name of results for compilation.
	 */
	const RESULTS_COMPILER = 'compiler';

        /**
         * Enable style verification (pep8).
         */
        private $style = TRUE;

	/**
	 * Name of results for style verification.
	 */
	const RESULTS_STYLE = 'style';

        /**
         * Enable execution of test cases.
         */
        private $test = TRUE;

	/**
	 * Name of results for software testing.
	 */
	const RESULTS_TEST = 'test.xml';

	/**
	 * Name of results for software testing coverage.
	 */
	const RESULTS_TEST_COVERAGE = 'test-coverage.xml';

	/**
	 * Name of results for software testing coverage (HTML).
	 */
	const RESULTS_TEST_COVERAGE_HTML = 'test-coverage';


	private function setDefaultBlacklist() {
		$this->blacklist[] = 'import os';
		$this->blacklist[] = 'os.';
		$this->blacklist[] = 'system';
		$this->blacklist[] = 'os.remove';
		$this->blacklist[] = 'os.rmdir';
		$this->blacklist[] = 'subprocess';
	}

	public function getName() {
		return 'Python grader based on software testing';
	}

	public function getFeatures() {
		$features = array();
		$features[] = 'SoftwareTesting_Driver_Nose';
		$features[] = 'SoftwareTesting_Criterion_StatementCoverage';
		$features[] = 'SoftwareTesting_Criterion_BranchCoverage';
		return $features;
	}

	public function getOutputFormat() {
		return 'XUnit XML';
	}
	
        public function canEvaluate($submission, $assignment = NULL) {
                $ext = pathinfo($submission->getFile(), PATHINFO_EXTENSION);
                if (strcasecmp($ext, 'py') == 0) {
                        return True;
                }
                return False;
        }

	public function evaluate($submission, $assignment = NULL) {
		$contents = file_get_contents($submission->getFile());
                foreach ($this->blacklist as $word) {
                        if (strstr($contents, $word) !== False) {
                                throw new Exception('The file has unsafe statements');
                        }
                }

		$assessment = new Assessment('Assessment using Nose-defined test cases');

		if ($this->compiler) {
		}

		if ($this->style) {
		}

		if ($this->test) {
			$comando = $this->nosecmd;
			$comando .= ' -q';
       		        $comando .= ' --with-coverage --cover-branches';
	       	        $comando .= ' --with-xunit --xunit-file=' . $submission->getWorkingDir() . '/' . self::RESULTS_TEST;
			$comando .= ' --cover-xml --cover-xml-file=' . $submission->getWorkingDir() . '/' . self::RESULTS_TEST_COVERAGE;
	       	        $comando .= ' --cover-html --cover-html-dir=' . $submission->getWorkingDir() . '/' . self::RESULTS_TEST_COVERAGE_HTML;
	                $comando .= ' ' . $submission->getFile();
			$cwd = getcwd();
	                chdir($submission->getWorkingDir());
	                exec($comando);
			chdir($cwd);

			$testsuite = simplexml_load_file($submission->getWorkingDir() . '/' . self::RESULTS_TEST);
			foreach ($testsuite->testcase as $testcase) {
				$partialAssessment = new Assessment($testcase['name']);
				if (! isset($testcase->failure)) {
					$partialAssessment->addSuccess();
				} else {
					foreach ($testcase->failure as $failure) {
						$partialAssessment->addError();
						$partialAssessment->addErrorMessage($failure['message']);
					}
				}
				$assessment->addPartialResult($partialAssessment);
			}	

			if (is_file($submission->getWorkingDir() . '/' . self::RESULTS_TEST_COVERAGE)) {
		                $xml = simplexml_load_file($submission->getWorkingDir() . '/' . self::RESULTS_TEST_COVERAGE);
				$assessment->setCoverage('Statement', floatval($xml['line-rate']));
				$assessment->setCoverage('Branch', floatval($xml['branch-rate']));
			}
		}

		return $assessment;
	}
}

?>
