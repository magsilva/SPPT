<html>

<?php
	require_once(__DIR__ . '/SPPTWeb.class.php');
	$spptweb = new SPPTWeb();
	$spptweb->setBaseDir(__DIR__);
	$spptweb->setBaseUrl('/tdd');
?>

<head>
	<title>SPPT (Small Python Program Tester)</title>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>


<form method="post" action="<?php basename(__FILE__); ?>" enctype="multipart/form-data">
	<label>RA do aluno (somente números):</label>
	<input type="text" name="<?php echo SPPTWeb::RA_INPUT; ?>" value="<?php if (isset($_REQUEST[SPPTWeb::RA_INPUT])) {echo $_REQUEST[SPPTWeb::RA_INPUT]; } ?>"/>

	<br />
	<label>Arquivo:</label>
	<input type="file" name="<?php echo SPPTWeb::FILENAME_INPUT; ?>" />
	<input type="submit" name="submit" value="Enviar" />
</form>


<?php
	try {
		$submission = $spptweb->processUploadRequest($_REQUEST, $_FILES);
		$assessment = $submission->getAssessment();
		echo "<h2>Execução dos casos de teste</h2>\n";
		echo "<ul>\n";
		foreach ($assessment as $testcase) {
			if (is_a($testcase, 'TestCaseResult')) {
				echo '<li><b>' . $testcase->getName() . ":</b>";
				if (! $testcase->hasFailed()) {
					echo "Ok\n";
				} else {
					echo "Erro: ";
					foreach ($testcase->getErrors() as $failure) {
						echo $failure;
					}
				}
				echo "</li>\n";
			}
		}
		echo "</ul>\n";

		echo "<br />\n";
		echo "<hr />";
		$baseSubmissionDir = substr($submission->getWorkingDir(), strlen($spptweb->getBaseDir()));
 		echo '<iframe width="95%" height="500" frameborder="1" src="' . $spptweb->getBaseUrl() .  '/' . $baseSubmissionDir . '/' . SPPT::RESULTS_TEST_COVERAGE_HTML . '/index.html"></iframe>';
		echo "\n";	
	} catch (Exception $e) {
		echo "<h2>Erros no envio da atividade</h2>\n";
		echo $e->getMessage();
	}
?>

</body>
</html>
