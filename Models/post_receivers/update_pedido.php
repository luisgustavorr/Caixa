<?php
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

include('../../MySql.php');
date_default_timezone_set('America/Sao_Paulo');
try {
    if($_POST['retirada'] == 1){
      $retirar = 'Sim';
    }else{
      $retirar = 'Não';

    }
    $connector = new WindowsPrintConnector(dest:"TM-T20X");

    $printer = new Printer($connector);

    $printer->text("PEDIDO\n");
  $printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
  $printer->text("Endereco:".$_POST['endereco']."\n");
  $printer->text("Cliente:".$_POST['cliente']."\n");
  $printer->text("Data do Pedido:".$_POST['data_pedido']."\n");
  $printer->text("Data da Entrega:".$_POST['data_entrega']."\n");
  $printer->text("Vai retirar?".$retirar."\n");

  $printer->text("-----------------------------------------\n");


  $conn = \MySql::conectar();
  $conn->beginTransaction();

  $pedido_id = $_POST['pedido'];
  $cliente = $_POST['cliente'];
  $produtos = json_encode($_POST['produtos']);
  $data_entrega = $_POST['data_entrega'];
  $data_pedido = $_POST['data_pedido'];
  $retirada = $_POST['retirada'];
  $forma_pagamento = $_POST['pagamento'];
  $endereco = $_POST['endereco'];
  $caixa = $_POST['caixa'];

  // Atualiza os dados do pedido na tabela tb_pedidos
  $updatePedidoQuery = "UPDATE `tb_pedidos`
                      SET `cliente` = ?, `produtos` = ?, `data_entrega` = ?, `data_pedido` = ?,
                      `retirada` = ?, `forma_pagamento` = ?, `endereco` = ?
                      WHERE `tb_pedidos`.`id` = ?";
  $stmtPedido = $conn->prepare($updatePedidoQuery);
  $stmtPedido->execute([$cliente, $produtos, $data_entrega, $data_pedido, $retirada, $forma_pagamento, $endereco, $pedido_id]);

  // Atualiza o valor na tabela tb_caixas
  $total_valor = 0;
  foreach ($_POST['produtos'] as $key => $value) {
    $valor_produto = $value['preco'] * $value['quantidade'];
    $total_valor += $valor_produto;
  }

  $updateCaixaQuery = "UPDATE tb_caixas
                     SET valor_atual = valor_atual - (SELECT SUM(valor) FROM tb_vendas WHERE pedido_id = ?) + ?
                     WHERE caixa = ? AND (SELECT COUNT(*) FROM tb_vendas WHERE pedido_id = ?) > 0";
  $stmtCaixa = $conn->prepare($updateCaixaQuery);
  $stmtCaixa->execute([$pedido_id, $total_valor, $caixa, $pedido_id]);

  // Deleta os registros da tabela tb_vendas onde pedido_id é igual ao valor desejado
  $deleteVendasQuery = "DELETE FROM tb_vendas WHERE `tb_vendas`.`pedido_id` = ?";
  $stmtDeleteVendas = $conn->prepare($deleteVendasQuery);
  $stmtDeleteVendas->execute([$pedido_id]);

  // Insere os novos valores na tabela tb_vendas
  foreach ($_POST['produtos'] as $key => $value) {
    $insertVendasQuery = "INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`, `produto`, `forma_pagamento`, `pedido_id`)
                          VALUES (NULL, 'luis', ?, ?, ?, ?, ?, ?)";
    $stmtInsertVendas = $conn->prepare($insertVendasQuery);
    $stmtInsertVendas->execute([date("Y-m-d h:i:sa"), $value['preco'] * $value['quantidade'], $caixa, $value['id'], $forma_pagamento, $pedido_id]);

  $printer->text($value['quantidade'].'X-'.$value['id']." R$".$value['preco']."\n");

  }

  // Commit da transação
  $conn->commit();

  // Fecha a conexão
  $conn = null;



  $printer->cut();
  $printer->close();
} catch (Exception $e) {
  echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
};
