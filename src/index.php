<html>

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

	require_once(__DIR__ . '/SPPTWeb.class.php');
	$spptweb = new SPPTWeb();
	$spptweb->setBaseUrl('/tdd');
	$spptweb->setDatadir( __DIR__ . '/uploads');
	$submission = $spptweb->processUploadRequest($_REQUEST, $_FILES);

	// Se O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
	if ($submission != NULL && $submission->getResults() != NULL) {
		$results = $submission->getResults();

/*
// TODO: refactor submission -> message/test case results
	$submission['resultados_testes'] = $submission['pasta_submission'] . '/' . 'test.xml';
		echo "<h2>Execução dos casos de teste</h2>\n";		
		$xml = simplexml_load_file($results['resultados_testes']);
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
*/		
		echo "<br />\n";
		echo "<hr />";
		// TODO: subtract part of getWorkingDir() from the address below
 		echo '<iframe width="95%" height="500" frameborder="1" src="' . str_replace(__DIR__, "", $spptweb->getBaseUrl() . '/' . $submission->getWorkingDir() . '/' . SPPT::RESULTS_TEST . '/' . SPPT::RESULTS_TEST_COVERAGE . '/index.html"></iframe>';
		echo "\n";	
	}
?>

</body>
</html>
