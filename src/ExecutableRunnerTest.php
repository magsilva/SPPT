<?php

require_once('ExecutableRunner.class.php');

$runner = new ExecutableRunner("/tmp");
$param = array();
$param[] = "-la";
$file = "/home/magsilva/teste.txt";
$runner->execute('/usr/bin/ls', $param, NULL, NULL, $file);


$runner = new ExecutableRunner("/tmp");
$param = array();
$param[] = "300";
$file = "/home/magsilva/teste2.txt";
$runner->execute('/usr/bin/sleep', $param, NULL, NULL, $file);

?>
