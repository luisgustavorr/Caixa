<?php require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

use Mike42\Escpos\Printer;

include('../../MySql.php');
date_default_timezone_set('America/Sao_Paulo');

foreach ($_POST['produtos'] as $key => $value) {
  $produto = \MySql::conectar()->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`) VALUES (NULL, ?, ?, ?, ?,?,?); ");
  $produto->execute(array($_POST['colaborador'],date("Y-m-d h:i:sa"),$value['preco']*$value['quantidade'],$_POST['caixa'],$value['id'],$_POST['pagamento']));
  $atualizar_caixa = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ? ");
  $atualizar_caixa->execute(array($_POST['valor'],$_POST['caixa']));

}
$connector = new WindowsPrintConnector(dest:"TM-T20X");

$printer = new Printer($connector);
  $drawerCommand = "\x1B\x70\x00\x19\xFA";

  $connector->write($drawerCommand);


$printer->close();
?>