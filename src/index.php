<html>

<?php
require_once(__DIR__ . '/SPPTWeb.class.php');
$spptweb = new SPPTWeb();
$spptweb->setBaseDir(__DIR__);
$spptweb->setBaseUrl('/apps/sppt');
$spptweb->setBaseUploadDir($spptweb->getBaseDir() . '/upload');
$assignmentDirectory = new AssignmentDirectory();
$assignmentDirectory->setBaseDir($spptweb->getBaseDir() . '/assignments');
$bundleId = $spptweb->getBundleId($_REQUEST);
$availableBundles = $assignmentDirectory->getBundles();
$userId = $spptweb->getUserId($_REQUEST);
$assignments = [];
if ($bundleId != NULL) {
	$assignments = $assignmentDirectory->getAssignmentsFor($bundleId);
}
$assignmentId = $spptweb->getAssignmentId($_REQUEST);
?>

<head>
	<title>SPPT (SimPle Program Tester)</title>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>

<h1>Avaliador de modelos especificados com JFLAP</h1>
<br />


<?php
if ($bundleId == NULL) {
	echo "\n<h2>Tarefas disponíveis</h2>";
	echo "\n<ul>";
	foreach ($availableBundles as $bundle) {
		echo "\n\t<li><a href=\"" . basename(__FILE__) . '?' . SPPTWeb::ASSIGNMENT_BUNDLE_INPUT . '=' . $bundle['id'] . "\">" . $bundle['name'] . "</a></li>";
	}
	echo "\n</ul>";
?>


<?php
} else {
?>

<form method="post" action="<?php basename(__FILE__) . '?' . SPPTWeb::ASSIGNMENT_BUNDLE_INPUT . '=' . $bundleId; ?>" enctype="multipart/form-data">
	<p>
	<label for="<?php echo SPPTWeb::RA_INPUT; ?>">RA do aluno:</label>
	<br /><input type="text" name="<?php echo SPPTWeb::RA_INPUT; ?>" value="<?php if ($userId != NULL) {echo $userId; } ?>" />
	</p>

	<p>
	<label for="<?php echo SPPTWeb::ASSIGNMENT_INPUT; ?>">Tarefa:</label>
	<br />
	<select name="<?php echo SPPTWeb::ASSIGNMENT_INPUT; ?>">
<?php
	foreach ($assignments as $assignment) {
		echo "\n\t<option value=\"" . htmlspecialchars($assignment->getId()) . "\"" . " title=\"" . htmlspecialchars($assignment->getDescription()) . "\"";
		if ($assignmentId != NULL && $assignment->getId() == $assignmentId) {
			echo " selected";
		}
		echo ">" . htmlspecialchars($assignment->getName());
		echo "</option>";
	}
?>
	</select> 
	</p>

	<p>
	<label for="file">Arquivo (.jflap ou .txt):</label>
	<br />
	<input type="file" name="<?php echo SPPTWeb::FILENAME_INPUT; ?>" />
	</p>

	<p>
	<input type="submit" name="submit" value="Enviar arquivo e avaliar automaticamente" />
	</p>
</form>

<?php
	if ($spptweb->hasSomethingToProcess($_REQUEST, $_FILES)) {
		$assignments = $assignmentDirectory->getSpecificAssignmentsFor($bundleId, $assignmentId);
		if ($assignments != NULL && count($assignments) > 0) {
			try {
				$assessmentsBundles = $spptweb->processUploadRequest($_REQUEST, $_FILES, $assignments);
				echo "<h2>Execução dos casos de teste</h2>\n";
				echo "<ul>\n";
				foreach ($assessmentsBundles as $assessmentsBundle) {
					foreach ($assessmentsBundle as $outputFormat => $assessments) {
						if ($outputFormat == 'XUnit XML') {
							echo '<li><b>' . htmlspecialchars($assessments->getName()) . '</b>';
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
								echo "<br />Erros encontrados: " . htmlspecialchars($assessments->getErrors()) . "\n";
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
							echo "<p></p></li>\n";
						}
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
	}
}
?>

</body>
</html>
