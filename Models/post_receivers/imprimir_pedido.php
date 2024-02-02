<?php 
include('../../MySql.php');

require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
$colabStmt = \MySql::conectar()->prepare("SELECT * FROM `tb_caixas` INNER JOIN `tb_equipamentos` ON `tb_equipamentos`.`caixa` = `tb_caixas`.`caixa`  WHERE `tb_caixas`.`caixa` = ?");
$colabStmt->execute(array($_POST['caixa']));
$caixa = $colabStmt->fetch();

$pedidos = \MySql::conectar()->prepare("SELECT * FROM `tb_pedidos`   WHERE `caixa` = ? AND  `enviado` = 1");
$pedidos->execute(array($_POST['caixa']));
$pedidos = $pedidos->fetchAll();
foreach ($pedidos as $key => $pedido) {
    
    # code...
    if ($pedido['retirada'] == 1) {
        $retirar = 'Sim';
    } else {
        $retirar = 'Não';
    }
$connector = new WindowsPrintConnector(dest: 'TM-T20X');
$printer = new Printer($connector);
$printer->setTextSize(2, 2);
$printer->setFont(Printer::FONT_B);
$printer->setLineSpacing(50);
$printer->setEmphasis(true);
$printer = new Printer($connector);
$printer->setTextSize(2, 2);
$printer->setFont(Printer::FONT_B);
$printer->setLineSpacing(50);
$printer->setEmphasis(true);

$printer->text("PEDIDO\n");
$printer->setEmphasis(false);
$printer->text("--------------------------------\n");

$printer->text("Cliente:" . $pedido['cliente'] . "\n\n");
$printer->text("Número cliente:" . $pedido['numero_cliente'] . "\n\n");
$printer->text("Código Funcionário:" . $pedido['colaborador'] . "\n\n");
$printer->text("Valor Entrada:" . $pedido['valor_entrada'] . "\n\n");

list($dataPedido, $horaPedido) = explode(' ', $pedido['data_pedido']);
$printer->text("Data do Pedido:" . date("d-m-Y", strtotime($dataPedido)) . "\n\n");
$printer->text("Hora do Pedido:" . $horaPedido . "\n\n");

list($dataEntrega, $horaEntrega) = explode(' ', $pedido['data_entrega']);
$printer->text("Data da Entrega:" . date("d-m-Y", strtotime($dataEntrega)) . "\n\n");
$printer->text("Hora da Entrega:" . $horaEntrega . "\n\n");

$printer->text("Entrega? " . $retirar . "\n\n");
if ($retirar == 'Sim') {
  $printer->text("Endereco:" . $pedido['endereco'] . "\n\n");
}

$printer->text("-> NÃO É DOCUMENTO FISCAL <-\n\n");
$printer->text("--------------------------------\n");
$printer->text("Item\n\n");
print_r(json_decode($pedido['produtos']));
$result = json_decode($pedido['produtos']);
print_r(json_decode($pedido['produtos'], true));
$valor_total = 0;
foreach (json_decode($pedido['produtos'], true) as $key => $value) {
  $printer->text($value['quantidade'] . '-' . str_replace('_', ' ', $value['id']) . " R$" . $value['preco'] . "\n\n");
  $valor_total += $value['preco'];
}
$printer->text("Valor Total:R$" . number_format($valor_total, 2, ',', '.') . "\n");
$printer->text("#Pedido de número " . $pedido['id'] . "\n");

// Finaliza a impressão e fecha a conexão
$printer->cut();
$printer->close();

$colabStmt = \MySql::conectar()->prepare("UPDATE `tb_pedidos` SET  `enviado` = 2   WHERE id = ?");
$colabStmt->execute(array($pedido['id']));
}
