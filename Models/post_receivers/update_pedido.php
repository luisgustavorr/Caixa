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
    $colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
    $colab->execute(array($_POST['codigo_colaborador']));
    $colab = $colab->fetch();
    
      $caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
    $caixa->execute(array(trim($colab['caixa'])));
    $caixa = $caixa ->fetch();
      @$connector = new WindowsPrintConnector(dest:$caixa['impressora']);
    
      @$printer = new Printer($connector);
      $printer -> setTextSize(2, 2);
    
    $printer -> setFont(Printer::FONT_B);
    
      $spacing = 20; // Ajuste o valor conforme necessário
    
      $printer->setLineSpacing(50);
    
    $printer->setEmphasis(true); // Desativa o modo de enfatizar (negrito)
    
      $printer->text("PEDIDO\n");
    $printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
    $printer->text("--------------------------------\n");
    
    $printer->text("Cliente:".$_POST['cliente']."\n");
    $printer->text("Código Funcionário:".$_POST['codigo_colaborador']."\n");
    
    $printer->text("Valor Entrada:".$_POST['valor_entrada']."\n");
    
    $printer->text("Cliente:".$_POST['cliente']."\n");
    @$printer->text(" \n");
    
    list($dataPedido, $horaPedido) = explode(' ', $_POST['data_pedido']);
    $printer->text("Data do Pedido:".$dataPedido."\n");
    @$printer->text(" \n");
    
    $printer->text("Hora do Pedido:".$horaPedido."\n");
    @$printer->text(" \n");
    
    echo $dataPedido, $horaPedido;
    list($dataEntrega, $horaEntrega) = explode(' ', $_POST['data_entrega']);
    $printer->text("Data da Entrega:".$dataEntrega."\n");
    @$printer->text(" \n");
    
    $printer->text("Hora da Entrega:".$horaEntrega."\n");
    @$printer->text(" \n");
    
    echo $dataEntrega, $horaEntrega;
    
    $printer->text("Entrega? ".$retirar."\n");
    @$printer->text(" \n");
    
    if($retirar == 'Sim'){
    $printer->text("Endereco:".$_POST['endereco']."\n");
    
    }
    @$printer->text(" \n");
    
    $printer->text("-> NÃO É DOCUMENTO FISCAL <-\n");
    @$printer->text(" \n");
    
    $printer->text("--------------------------------\n");
    $printer->text("Item\n");
    @$printer->text(" \n");
    
    

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
  $numero_cliente = $_POST['numero_cliente'];


  // Atualiza os dados do pedido na tabela tb_pedidos
  $updatePedidoQuery = "UPDATE `tb_pedidos`
                      SET `cliente` = ?, `produtos` = ?, `data_entrega` = ?, `data_pedido` = ?,
                      `retirada` = ?, `forma_pagamento` = ?, `endereco` = ?, `numero_cliente` = ?
                      WHERE `tb_pedidos`.`id` = ?";
  $stmtPedido = $conn->prepare($updatePedidoQuery);
  $stmtPedido->execute([$cliente, $produtos, $data_entrega, $data_pedido, $retirada, $forma_pagamento, $endereco,$numero_cliente, $pedido_id]);

  // Atualiza o valor na tabela tb_caixas
  $total_valor = 0;
  foreach ($_POST['produtos'] as $key => $value) {
    $valor_produto = $value['preco'] ;
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
    $stmtInsertVendas->execute([date("Y-m-d h:i:sa"), $value['preco'] , $caixa, $value['id'], $forma_pagamento, $pedido_id]);

    $printer->text( $value['quantidade'].'-'.str_replace('_',' ',$value['id'])." R$".$value['preco']."\n");
    @$printer->text(" \n");

  }
  $printer->text("Valor Total:R$".number_format($total_valor,2,',','.')."\n");
  $printer->text("#Pedido de número ".$pedido_id."\n");
  // Commit da transação
  $conn->commit();

  // Fecha a conexão
  $conn = null;



  $printer->cut();
  $printer->close();
} catch (Exception $e) {
  echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
};
