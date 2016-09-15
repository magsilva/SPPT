<?php

require_once(__DIR__ . '/SPPT.class.php');
require_once(__DIR__ . '/Submission.class.php');

class CSVGenerator {

	private $resultCsv;


	public function setResultsCsv($file)
        {
                if (is_string($file)) {
                        $file = fopen($this->workingDir . '/'. basename($file), 'a');
                        if ($file !== FALSE) {
                                $this->resultsCsv = $result;
                        }
                } else if (is_resource($file) && (get_resource_type($file) == 'file' || get_resource_type($file) == 'stream')) {
                        $this->resultsCsv = $file;
                }
        }

        public function getResultsCsv()
        {
                return stream_get_meta_data($this->resultsCsv)['uri'];
        }

	public function process($dir, $file, $ra, $timestamp)
	{
		$sppt = new SPPT();
		$sppt->setDatadir($dir);
		$submission = new Submission($sppt->getDatadir(), $ra, $timestamp);
		$submission->setFile($file);
		$sppt->assess($submission);

		var_dump($submission->stmtCoverage, $submission->branchCoverage);

	}

	public function batchProcess($dir)
	{
		$baseDir = $dir;
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $raDir) {
			if ($raDir->isDir() && ! $raDir->isDot()) {
				$ra = $raDir->getFilename();
				$timestampIterator = new DirectoryIterator($raDir->getPathname());
				foreach ($timestampIterator as $timestampDir) {
					if ($timestampDir->isDir() && ! $timestampDir->isDot()) {
						$timestamp = $timestampDir->getFilename();
						$pythonIterator = new DirectoryIterator($timestampDir->getPathname());
						foreach ($pythonIterator as $pythonFile) {
							if ($pythonFile->isFile() && $pythonFile->getExtension() == 'py') {
								$this->process($baseDir, $pythonFile->getFilename(), $ra, $timestamp);
							}
						}
					}
				}
			}
		}
	}
}

$test = new CSVGenerator();
$test->batchProcess('/var/www/html/tdd/uploads');

?>
