<?php

require(__DIR__ . '/TestCaseResult.class.php');

/**
 * Small Python Program Tester.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class SPPT
{
	private $baseurl = '/tdd';

	private $nosecmd = '/usr/bin/nosetests';

	/**
	 * Configuration of $datadir (directory where files will be saved to).
	 */
	private $datadir;

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
	const RESULTS_TEST_COVERAGE = 'coverage';


	private function setDefaultBlacklist() {
		$this->blacklist[] = 'import os';
		$this->blacklist[] = 'os.';
		$this->blacklist[] = 'system';
		$this->blacklist[] = 'os.remove';
		$this->blacklist[] = 'os.rmdir';
		$this->blacklist[] = 'subprocess';
	}

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


	public function assess($submission) {
		$contents = file_get_contents($submission->getFile());
                foreach ($this->blacklist as $word) {
                        if (strstr($contents, $word) !== False) {
                                throw new Exception('The file has unsafe statements');
                        }
                }

		$assessment = array();


		if ($this->compiler) {
		}

		if ($this->style) {
		}

		if ($this->test) {
			$comando = $this->nosecmd;
       		        $comando .= ' --with-coverage --cover-branches';
	       	        $comando .= ' --cover-html --cover-html-dir=' . $submission->getWorkingDir() . '/' . SPPT::RESULTS_TEST_COVERAGE;
	       	        $comando .= ' --with-xunit --xunit-file=' . $submission->getWorkingDir() . '/' . SPPT::RESULTS_TEST;
	                $comando .= ' ' . $submission->getFile();
			$cwd = getcwd();
	                chdir($submission->getWorkingDir());
	                exec($comando);
			chdir($cwd);

	                $xml = simplexml_load_file($submission->getWorkingDir() . '/' . SPPT::RESULTS_TEST);
	                foreach ($xml->testcase as $testcase) {
				$testStatus = isset($testcase['failure']) ? True : False;  // Test cases should fail if the intend is to find errors :-)
				$testResult = new TestCaseResult($testcase['name'], $testStatus);
	                        foreach ($testcase->failure as $failure) {
					$testResult->addError($failure['message']);
   				}
				$assessment[] = $testResult;
			}	
		}

		$submission->setAssessment($assessment);
		return $submission;
	}
}

?>
