<?php 
include('../../MySql.php');

  $produto = \MySql::conectar()->prepare("SELECT `valor_atual` AS 'Valor_total' FROM `tb_caixas` WHERE caixa = ?;");
  $produto->execute(array($_POST['caixa']));
  $produto = $produto->fetch();
  echo $produto['Valor_total']
  

?>