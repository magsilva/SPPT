<?php

/**
 * Small Python Program Tester.
 *
 * @author Marco Aurélio Graciotto Silva
 */
class SPPT
{
	private $baseurl = '/tdd';

	private $nosecmd = '/bin/nosetests';

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
	const RESULTS_TEST = 'test';

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


	public function extract($submission) {
		$contents = file_get_contents($submission->getFile());
                foreach ($this->blacklist as $word) {
                        if (strstr($contents, $word) !== False) {
                                throw new Exception('The file has unsafe statements');
                        }
                }


		if ($compiler) {
		}

		if ($style) {
		}

		if ($test) {
			$comando = $nosecmd;
       		        $comando .= ' --with-coverage --cover-branches';
	       	        $comando .= ' --cover-html --cover-html-dir=' . $submission->getWorkingDir() . '/' . SPPT::RESULTS_TEST . '/' . SPPT::RESULTS_TEST_COVERAGE;
	       	        $comando .= ' --with-xunit --xunit-file=' . $submission->getWorkingDir() . '/' . SPPT::RESULTS_TEST;
	                $comando .= ' ' . $submission->getFile();
			$cwd = getcwd();
	                chdir($submission[$submission->getWorkingDir());
	                exec($comando, $log_teste);
			chdir($cwd);

	                $xml = simplexml_load_file($submission->getWorkingDir() . '/' . SPPT::RESULTS_TEST . '/' . 'test.xml';
	                foreach ($xml->testcase as $testcase) {
	                    echo '<b>' . $testcase['name'] . ":</b>";
	                    if (! isset($testcase->failure)) {
	                        echo "Ok<br />\n";
	                    } else {
	                        echo "Erro!\n";
	                        echo "<ul>\n";
	                        foreach ($testcase->failure as $failure) {
	                            echo "\t<li>" . $failure['message'] . "</li>\n";
	                        }
	                        echo "</ul><br />\n";
	                    }
			}
		}
	}
}

?>
