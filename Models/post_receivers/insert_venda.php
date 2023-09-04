<?php 



include('../../MySql.php');
date_default_timezone_set('America/Sao_Paulo');
$colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
$colab->execute(array($_POST['colaborador']));
$colab = $colab->fetch();

$caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
$caixa->execute(array($colab['caixa']));
$caixa = $caixa ->fetch();
if(!empty($colab)){
  unset($_COOKIE['caixa']);
  setcookie("caixa", $colab['caixa'], time()+20*24*60*60);
  foreach ($_POST['produtos'] as $key => $value) {
    $produto = \MySql::conectar()->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`) VALUES (NULL, ?, ?, ?, ?,?,?); ");
    $produto->execute(array($_POST['colaborador'],date("Y-m-d h:i:sa"),$value['preco']*$value['quantidade'],trim($colab['caixa']),$value['id'],$_POST['pagamento']));
    $atualizar_caixa = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ? ");
    $atualizar_caixa->execute(array($_POST['valor'],trim($colab['caixa'])));
  
  }


}else{
  echo 'Código de vendedor inválido';
}

?>