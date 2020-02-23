<html>

<?php
	require_once(__DIR__ . '/SPPTWeb.class.php');
	$spptweb = new SPPTWeb();
	$spptweb->setBaseDir(__DIR__);
	$spptweb->setBaseUrl('/apps/sppt');
	$spptweb->setBaseUploadDir($spptweb->getBaseDir() . '/upload');
?>

<head>
	<title>SPPT (SimPle Program Tester)</title>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>

<h1>Avaliador de modelos especificados com JFLAP</h1>
<br />

<form method="post" action="<?php basename(__FILE__); ?>" enctype="multipart/form-data">
	<p>
	<label>RA do aluno (somente números):</label>
	<br /><input type="text" name="<?php echo SPPTWeb::RA_INPUT; ?>" value="<?php if (isset($_REQUEST[SPPTWeb::RA_INPUT])) {echo $_REQUEST[SPPTWeb::RA_INPUT]; } ?>"/>
	</p>

	<p>
	<label>Arquivo (.jflap ou .txt):</label>
	<br />
	<input type="file" name="<?php echo SPPTWeb::FILENAME_INPUT; ?>" />
	</p>

	<p>
	<input type="submit" name="submit" value="Enviar arquivo e avaliar automaticamente" />
	</p>
</form>


<?php
	if ($spptweb->hasSomethingToProcess($_REQUEST, $_FILES)) {
		try {
			$assessmentsBundle = $spptweb->processUploadRequest($_REQUEST, $_FILES);
			echo "<h2>Execução dos casos de teste</h2>\n";
			echo "<ul>\n";
			foreach ($assessmentsBundle as $outputFormat => $assessments) {
				if ($outputFormat == 'XUnit XML') {
					echo '<li><b>' . $assessments->getName() . ': </b>';
					if (count($assessments->getCoverage()) != 0) {
						echo '<br />Cobertura (conforme o critério)';
						echo '<ul>';
						foreach ($assessments->getCoverage() as $criterion => $coverage) {
							echo "\t\t" . '<li>' . htmlspecialchars($criterion) . ': ' . htmlspecialchars($coverage) . '</li>' . "\n";
						}
						echo '</ul>';
					}
					if (! $assessments->hasFailed()) {
						echo "<br />Não foram encontrados erros.\n";
					} else {
						echo "<br />Erros encontrados: " . $assessments->getErrors() . "\n";
						echo "<ul>";
						foreach ($assessments->getPartialResults() as $assessment) {
							if ($assessment->hasFailed()) {
								echo "\t<li>" .  htmlspecialchars($assessment->getName()) . "\n";
								echo "\t\t<ul>\n";
								foreach ($assessment->getErrorMessages() as $failure) {
									echo "\t\t\t<li>" . htmlspecialchars($failure) . '</li>' . "\n";
								}
								echo "\t\t</ul>\n";
								echo "\t</li>\n";
							}
						}
						echo "</ul>\n";
					}
					echo "</li>\n";
				}
			}
			echo "</ul>\n";

			/*
			echo "<br />\n";
			echo "<hr />";
			$baseSubmissionDir = substr($submission->getWorkingDir(), strlen($spptweb->getBaseDir()));
			echo '<iframe width="95%" height="500" frameborder="1" src="' . $spptweb->getBaseUrl() .  '/' . $baseSubmissionDir . '/' . SPPT::RESULTS_TEST_COVERAGE_HTML . '/index.html"></iframe>';
			echo "\n";
			*/
		} catch (Exception $e) {
			echo "<h2>Erros no envio da atividade</h2>\n";
			echo $e->getMessage();
		}
	}
?>

</body>
</html>
