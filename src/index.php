<html>

<head>
	<title>SPPT (Small Python Program Tester)</title>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>

<?php

$baseurl = '/tdd';
# $baseurl = '';

$nosecmd = '/bin/nosetests';

/**
 * Configuration of $datadir (directory where files will be saved to).
 */
$datadir = __DIR__ . '/uploads';
if (! file_exists($datadir)) {
	$result = mkdir($datadir, 0700, true);
	if ($result === False) {
		echo "Cannot create directory to save files: " . $datadir;
		exit();
	}
}
if (! is_dir($datadir)) {
	echo "Cannot use directory to save files";
	exit();
}


/**
 * Configuration of $maxfilesize (maximum byte size of uploaded file).
 */
$maxFileSize= 1024 * 1024 * 2; 


/**
 * Supported file extensions.
 */
$allowedFileExtensions = array();
$allowedFileExtensions[] = 'py';

/**
 * Error messages regarding file upload.
 */
$uploadErrorMessages = array();
$uploadErrorMessages[0] = 'Não houve erro';
$uploadErrorMessages[1] = 'O arquivo no upload é maior do que o limite do PHP';
$uploadErrorMessages[2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$uploadErrorMessages[3] = 'O upload do arquivo foi feito parcialmente';
$uploadErrorMessages[4] = 'Não foi feito o upload do arquivo';

/**
 * Blacklisted statements.
 */
$blacklist = array();
$blacklist[] = 'import os';
$blacklist[] = 'os.';
$blacklist[] = 'system';
$blacklist[] = 'os.remove';
$blacklist[] = 'os.rmdir';
$blacklist[] = 'subprocess';

?>

 
<form method="post" action="index.php" enctype="multipart/form-data">
	<label>RA do aluno (somente números):</label>
	<input type="text" name="ra" value="<?php if (isset($_REQUEST['ra'])) {echo $_REQUEST['ra']; } ?>"/> <p />
	<label>Arquivo:</label>
	<input type="file" name="arquivo" />
	<input type="submit" name="submit" value="Enviar" />
</form>


<?php
if (isset($_REQUEST['submit'])) {
	// Faz a verificação da extensão do arquivo
	$envioOk = True;


	// Verifica se foi informado um RA válido
	if (! isset($_REQUEST['ra'])) {
		echo "RA não foi informado <br />\n";
		$envioOk = False;
	}
	
	if (! is_numeric($_REQUEST['ra'])) {
		echo 'RA inválido: ' . $_REQUEST['ra'] . "<br />\n";
		$envioOk = False;
	}

	// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
	if ($_FILES['arquivo']['error'] != 0) {
		echo "Não foi possível fazer o upload: " . $uploadErrorMessages[$_FILES['arquivo']['error']] . "<br />\n";
		$envioOk = False;
	} else { 
		$ext = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);
		if (! in_array($ext, $allowedFileExtensions)) {
			echo "Por favor, apenas envie arquivos com extensao .py. <br />\n";
			$envioOk = False;
		}
	 
		// Faz a verificação do tamanho do arquivo
		if ($_FILES['arquivo']['size'] > $maxFileSize) {
			echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb. <br />\n";
			$envioOk = False;
		}

		$contents = file_get_contents($_FILES['arquivo']['tmp_name']);
		foreach ($blacklist as $word) {
			if (strstr($contents, $word) !== False) {
				echo "O arquivo contém um comando proibido. <br />\n";
				$envioOk = False;
			}
		}
	}

	

	// Se O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
	if ($envioOk) {
		$submission['pasta_usuario'] = $datadir . '/' . $_REQUEST['ra'];
		if (! file_exists($submission['pasta_usuario'])) {
			mkdir($submission['pasta_usuario'], 0700);
		}

		$submission['pasta_submission'] = $submission['pasta_usuario'] . '/' . time();
		if (! file_exists($submission['pasta_submission'])) {
			mkdir($submission['pasta_submission'], 0700);
		}

		$submission['arquivo'] = $submission['pasta_submission'] . '/' . $_FILES['arquivo']['name'];

		$submission['resultados_cobertura'] = $submission['pasta_submission'] . '/' . 'cover';
		if (! file_exists($submission['resultados_cobertura'])) {
			mkdir($submission['resultados_cobertura'], 0700);
		}

		$submission['resultados_testes'] = $submission['pasta_submission'] . '/' . 'test.xml';

		// Depois verifica se é possível mover o arquivo para a pasta escolhida
		if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $submission['arquivo'])) {
			// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
			echo "<h2>Execução dos casos de teste</h2>\n";
				
			$comando = $nosecmd;
			$comando .= ' --with-coverage --cover-branches';
			$comando .= ' --cover-html --cover-html-dir=' . $submission['resultados_cobertura'];
			$comando .= ' --with-xunit --xunit-file=' . $submission['resultados_testes'];
			$comando .= ' ' . $submission['arquivo'];
			chdir($submission['pasta_submission']);
			exec($comando, $log_teste);

			$xml = simplexml_load_file($submission['resultados_testes']);
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
			
			echo "<br />\n";
			echo "<hr />";
    			echo '<iframe width="95%" height="500" frameborder="1" src="' . str_replace(__DIR__, "", $baseurl . $submission['resultados_cobertura']) . '/index.html"></iframe>';
			echo "\n";
		
		} else {
			// Não foi possível fazer o upload, provavelmente a pasta está incorreta
			echo "<b>Não foi possível enviar o arquivo, tente novamente</b>\n";
		}
 	} else {
		// Não foi possível fazer o upload, provavelmente a pasta está incorreta
                echo "<b>Não foi possível avaliar o trabalho enviado, tente novamente</b>\n";
	}
}
?>

</body>
</html>
