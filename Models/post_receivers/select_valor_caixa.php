<?php 
include('../../MySql.php');
  $produto = \MySql::conectar()->prepare("SELECT `valor_atual` AS 'Valor_total' FROM `tb_caixas` WHERE caixa = '".trim($_POST['caixa'])."'");
  $produto->execute();
  $produto = $produto->fetch();
  echo $produto['Valor_total']
  

?>