<?php 
require __DIR__ . '/../../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
include('../../MySql.php');
date_default_timezone_set('America/Sao_Paulo');
try{
  if($_POST['retirada'] == 1){
    $retirar = 'Sim';
  }else{
    $retirar = 'Não';

  }
  $caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
$caixa->execute(array(trim($_COOKIE['caixa'])));
$caixa = $caixa ->fetch();
  @$connector = new WindowsPrintConnector(dest:$caixa['impressora']);

  @$printer = new Printer($connector);

  $printer->text("PEDIDO\n");
$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
$printer->text("Cliente:".$_POST['cliente']."\n");
$printer->text("Código Funcionário:".$_POST['codigo_colaborador']."\n");

$printer->text("Valor Entrada:".$_POST['valor_entrada']."\n");

$printer->text("Cliente:".$_POST['cliente']."\n");

$printer->text("Data do Pedido:".$_POST['data_pedido']."\n");
$printer->text("Data da Entrega:".$_POST['data_entrega']."\n");
$printer->text("Entrega ?".$retirar."\n");
if($retirar == 'Sim'){
$printer->text("Endereco:".$_POST['endereco']."\n");

}
$printer->text("-----------------------------------------\n");


   $pedido = \MySql::conectar()->prepare(" INSERT INTO `tb_pedidos` (`id`, `cliente`, `produtos`, `data_entrega`, `data_pedido`,`retirada`,`forma_pagamento`,`endereco`,`caixa`,`valor_entrada`,`metodo_entrada`,`colaborador`) VALUES (NULL, ?, ?,?, ?, ?,?,?,?,?,?,?)");
   $pedido->execute(array($_POST['cliente'],json_encode($_POST['produtos']),$_POST['data_entrega'],$_POST['data_pedido'],$_POST['retirada'],$_POST['pagamento'],$_POST['endereco'],$_COOKIE['caixa'],$_POST['valor_entrada'],$_POST['metodo_entrada'],$_POST['codigo_colaborador']));
   $lastInsertedId = \MySql::conectar()->lastInsertId();
   $valor_total = 0;

   foreach ($_POST['produtos'] as $key => $value) {
    $produto = \MySql::conectar()->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`,`pedido_id`,`quantidade_produto`) VALUES (NULL, ?,?, ?, ?, ?,?,?,?); ");
    $produto->execute(array($_POST['codigo_colaborador'],date("Y-m-d h:i:sa"),$value['preco']*$value['quantidade'],$_COOKIE['caixa'],$value['id'],$_POST['pagamento'],$lastInsertedId,$value['quantidade']));
    $atualizar_caixa = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ? ");
    $atualizar_caixa->execute(array($value['preco']*$value['quantidade'],$_COOKIE['caixa']));
      $produto = \MySql::conectar()->prepare("SELECT nome FROM `tb_produtos` WHERE  `id` =?");
    $produto->execute(array($value['id']));
    $produto = $produto->fetch();
    $printer->text( $value['quantidade'].'-'.$produto['nome']." R$".$value['preco']."\n");
    $valor_total =  $valor_total+($value['preco']*$value['quantidade']);
  };
  $printer->text("Valor Total:".$valor_total."\n");

// Finaliza a impressão e fecha a conexão
$printer->cut();
$printer->close();
} catch (Exception $e) {
  echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
};

?>