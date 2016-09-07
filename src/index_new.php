<html>
<head>
    <title>Upload de Arquivos com PHP</title>
	<meta charset="UTF-8" />
</head>
<body>
 
<form method="post" action="index.php" enctype="multipart/form-data">
<label>RA Aluno [somente números]:</label>
<input type="text" name="ra" value="<?php if (isset($_POST['ra'])) {echo $_POST['ra']; } ?>"/> <p />
<label>Arquivo:</label>
<input type="file" name="arquivo" />
<input type="submit" name="submit" value="Enviar" />
</form>

<?php
if (isset($_POST['submit'])) {
	// Pasta onde o arquivo vai ser salvo
	$_UP['pasta'] = 'uploads/';
	 
	// Tamanho máximo do arquivo (em Bytes)
	$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
	 
	// Array com as extensões permitidas
	$_UP['extensoes'] = array('py');
	 
	// Array com os tipos de erros de upload do PHP
	$_UP['erros'][0] = 'Não houve erro';
	$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
	$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
	$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
	$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
	 
	// Verifica se houve algum erro com o upload. Se sim, exibe a mensagem do erro
	if ($_FILES['arquivo']['error'] != 0) {
		die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']]);
		exit; // Para a execução do script
	}
	 
	// Caso script chegue a esse ponto, não houve erro com o upload e o PHP pode continuar
	 
	// Faz a verificação da extensão do arquivo
	$ext = pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION);

	#$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
	#if (array_search($extensao, $_UP['extensoes']) === false) {
	if ($ext != 'py') {
		echo "Por favor, apenas envie arquivos com extensao .py";
	}
	 
	// Faz a verificação do tamanho do arquivo
	else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
		echo "O arquivo enviado é muito grande, envie arquivos de até 2Mb.";
	}
	 
	// O arquivo passou em todas as verificações, hora de tentar movê-lo para a pasta
	else {
		if (file_exists ("uploads/".$_POST['ra'])) {
			$_UP['pasta'] = 'uploads/'.$_POST['ra'].'/';
		} else {
			// Se a pasta uploads nao existir ela eh criada
			if (!file_exists("uploads")) {
				mkdir("uploads",0777);
			}
			// Se a pasta RA nao existir ela eh criada
			if (mkdir("uploads/".$_POST['ra'], 0777)) {
				$_UP['pasta'] = 'uploads/'.$_POST['ra'].'/';
			} else {
				die("Não foi possível fazer o upload, a pasta do usuario nao pôde ser criada!");
				exit;
			}
		}
		
		$nomec = str_replace(".py","",$_FILES['arquivo']['name']);
		$nome_final = $nomec.'-'.time().'.py';
		 
		// Depois verifica se é possível mover o arquivo para a pasta escolhida
		if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
			// Upload efetuado com sucesso, exibe uma mensagem e um link para o arquivo
			echo "Upload efetuado com sucesso!";
			echo "<p /> ************************** <p />";
			echo "Execucao do teste: <p /><p />";
				
			$pypath = "nosetests --cover-html-dir=uploads/".$_POST['ra']."/cover/ --cover-html --with-xunit --xunit-file=uploads/".$_POST['ra']."/test.xml --with-coverage --cover-branches ";

			$teste = exec("$pypath".$_UP['pasta'].$nome_final,$log_teste);

			$mostra = "$pypath".$_UP['pasta'].$nome_final;
			
			//echo shell_exec("ls");
			//echo "<p/> $mostra <p/>";
			
			/*foreach ($log_teste as $line) {
				echo $line;
				echo "<br />";
			}*/

			$xml = simplexml_load_file("uploads/".$_POST['ra']."/test.xml");
			foreach ($xml->testcase as $testcase) {
			    echo "<p/><b>".$testcase[name]."</b>";
			    if (! isset($testcase->failure)) {
				echo "<br/>Ok";
			    } else {
				foreach ($testcase->failure as $failure) {
				    echo "<br/>Erro: $failure[message]";
				}
			    }
			    echo "\n";
			}
			
			echo "<p /> ************************** <p />";
    			echo "<iframe width=95% height=500 frameborder='1' src='uploads/".$_POST['ra']."/cover/index.html'></iframe>";
			
			// VAI IMPRIMIR OS RESULTADOS QUE QUEREMOS DO ARQUIVO AQUI EIN:
			// **************************************************************************
			
			$linhas = file("uploads/".$_POST['ra']."/cover/index.html");
			$v1 = 0;
			$incrementa = false;
			$contagem = 0;
			$informacao = "uploads/".$_POST['ra'].$nome_final.";";
			foreach($linhas as $item){
				$contagem += 1;
				if (strpos($item , ">Total</td>")) {
					$incrementa = true;
				}
				if ($incrementa == true) {
					$v1 += 1;
					switch($v1) {
						case 2: // statements
						case 3: // missing
						case 4: // excluded
						// case 5 a linha eh vazia
						case 6: // branches
						case 7: // partial
							$linha_final = $item;
							$linha_final = str_replace("<td>","",$linha_final);
							$linha_final = str_replace("</td>",";",$linha_final);
							$informacao = $informacao.trim($linha_final);
							break;
							
						case 9: // porcentagem coverage
							$linha_final2 = $item;
							$linha_nova2 = str_replace("%</td>",";",$linha_final2);
							$valor = strpos( $linha_final2 , ">" );
							$linha_nova3 = substr($linha_nova2, $valor+1, strlen($linha_nova2));
							
							$informacao = $informacao.$linha_nova3;

							// Cria o file
							$fp = fopen("parametros.txt", "a");
							
							// Escreve o file
							$escreve = fwrite($fp, $informacao);

							// Fecha o file
							fclose($fp);

							break;
					}
					if ($v1 == 9) {
						// Para de percorrer o HTML
						break;
					}
					
				}
			}


		       // ***************************************************************************

		} else {
			// Não foi possível fazer o upload, provavelmente a pasta está incorreta
			echo "Não foi possível enviar o arquivo, tente novamente";
		}
	 
	}
}
?>

</body>
</html>
