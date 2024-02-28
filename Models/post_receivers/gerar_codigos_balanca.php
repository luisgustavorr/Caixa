<?php 
$codigo = $_POST["codigo"];
$numeroSemVirgula = str_replace(',', '', $codigo);
$numeroFormatado = sprintf("%05d", $numeroSemVirgula);
$codigo = "2".$numeroFormatado;
echo $codigo
?>