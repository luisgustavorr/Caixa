<?php



include('../../MySql.php');
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

date_default_timezone_set('America/Sao_Paulo');
$colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
$colab->execute(array($_POST['colaborador']));
$colab = $colab->fetch();

$caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
$caixa->execute(array($colab['caixa']));
$caixa = $caixa->fetch();
@$connector = new WindowsPrintConnector(dest: $caixa['impressora']);

@$printer = new Printer($connector);
@$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)
@$printer->text("Mix Salgados\n");
@$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)

$data_atual =  date("Y-m-d h:i:sa");

if (!empty($colab)) {
  unset($_COOKIE['caixa']);
  unset($_COOKIE['last_codigo_colaborador']);
  setcookie("last_codigo_colaborador", $_POST['colaborador'], time() + 20 * 24 * 60 * 60);
  setcookie("caixa", $colab['caixa'], time() + 20 * 24 * 60 * 60);
  $valor_compra_total = 0;
  foreach ($_POST['produtos'] as $key => $value) {
    $valor_compra_total = $valor_compra_total + ($value['preco'] * $value['quantidade']);
    $produto = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `id` = ?");
    $produto->execute(array($value['id']));
    $produto = $produto->fetch();
    @$printer->text("Produto:" . $produto['nome'] . " Quantidade:" . $value['quantidade'] . " Valor: " . number_format($value['preco'] * $value['quantidade'], 2, ',', '.'));
    @$printer->text("-----------------------------------------\n");
    $produto = \MySql::conectar()->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`) VALUES (NULL, ?, ?, ?, ?,?,?); ");
    $produto->execute(array($_POST['colaborador'], $data_atual, $value['preco'] * $value['quantidade'], trim($colab['caixa']), $value['id'], $_POST['pagamento']));
    $atualizar_caixa = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ? ");
    $atualizar_caixa->execute(array($_POST['valor'], trim($colab['caixa'])));
  }
  @$printer->text("Valor total:" . number_format($valor_compra_total, 2, ',', '.'));
  $printer->text("-----------------------------------------\n");
  $printer->cut();
  $printer->close();
} else {
  echo 'Código de vendedor inválido';
}
