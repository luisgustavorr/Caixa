<?php
require __DIR__ . '/../../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
include('../../MySql.php');

date_default_timezone_set('America/Sao_Paulo');
$data_pedido = date("Y-m-d h:i:sa");

// Função para estabelecer a conexão com o banco de dados
function conectarAoBanco()
{
    return \MySql::conectar();
}

try {
    // Conectar ao banco de dados uma vez para reutilização
    $db = conectarAoBanco();

    if ($_POST['retirada'] == 1) {
        $retirar = 'Sim';
    } else {
        $retirar = 'Não';
    }

    // Obter dados do colaborador
    $colabStmt = $db->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
    $colabStmt->execute(array($_POST['codigo_colaborador']));
    $colab = $colabStmt->fetch();

    // Obter dados do equipamento
    $caixaStmt = $db->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
    $caixaStmt->execute(array(trim($colab['caixa'])));
    $caixa = $caixaStmt->fetch();

    $connector = new WindowsPrintConnector(dest: $caixa['impressora']);
    $printer = new Printer($connector);
    $printer->setTextSize(2, 2);
    $printer->setFont(Printer::FONT_B);
    $printer->setLineSpacing(50);
    $printer->setEmphasis(true);

    $printer->text("PEDIDO\n");
    $printer->setEmphasis(false);
    $printer->text("--------------------------------\n");

    $printer->text("Cliente:" . $_POST['cliente'] . "\n\n");
    $printer->text("Número cliente:" . $_POST['numero_cliente'] . "\n\n");
    $printer->text("Código Funcionário:" . $_POST['codigo_colaborador'] . "\n\n");
    $printer->text("Valor Entrada:" . $_POST['valor_entrada'] . "\n\n");

    list($dataPedido, $horaPedido) = explode(' ', $_POST['data_pedido']);
    $printer->text("Data do Pedido:" . date("d-m-Y", strtotime($dataPedido)) . "\n\n");
    $printer->text("Hora do Pedido:" . $horaPedido . "\n\n");

    list($dataEntrega, $horaEntrega) = explode(' ', $_POST['data_entrega']);
    $printer->text("Data da Entrega:" . date("d-m-Y", strtotime($dataEntrega)) . "\n\n");
    $printer->text("Hora da Entrega:" . $horaEntrega . "\n\n");

    $printer->text("Entrega? " . $retirar . "\n\n");

    if ($retirar == 'Sim') {
        $printer->text("Endereco:" . $_POST['endereco'] . "\n\n");
    }

    $printer->text("-> NÃO É DOCUMENTO FISCAL <-\n\n");
    $printer->text("--------------------------------\n");
    $printer->text("Item\n\n");

    $pedidoStmt = $db->prepare("INSERT INTO `tb_pedidos` (`id`, `cliente`, `produtos`, `data_entrega`, `data_pedido`,`retirada`,`forma_pagamento`,`endereco`,`caixa`,`valor_entrada`,`metodo_entrada`,`colaborador`,`numero_cliente`) VALUES (NULL, ?, ?,?, ?, ?,?,?,?,?,?,?,?)");
    $pedidoStmt->execute(array($_POST['cliente'], json_encode($_POST['produtos']), $_POST['data_entrega'], $_POST['data_pedido'], $_POST['retirada'], $_POST['pagamento'], $_POST['endereco'], $caixa['caixa'], $_POST['valor_entrada'], $_POST['metodo_entrada'], $_POST['codigo_colaborador'], $_POST['numero_cliente']));
    $lastInsertedId = $db->lastInsertId();
    $valor_total = 0;

    $insertEntradaStmt = $db->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`,`pedido_id`,`quantidade_produto`) VALUES (NULL, ?,?, ?, ?, ?,?,?,?); ");
    $insertEntradaStmt->execute(array($_POST['codigo_colaborador'], date("Y-m-d h:i:sa", strtotime($data_pedido) + 1), $_POST['valor_entrada'], $caixa['caixa'], 'Entrada Pedido_' . $lastInsertedId, $_POST['pagamento'], $lastInsertedId, 1));

    foreach ($_POST['produtos'] as $key => $value) {
        $produtoStmt = $db->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`,`pedido_id`,`quantidade_produto`) VALUES (NULL, ?,?, ?, ?, ?,?,?,?); ");
        $produtoStmt->execute(array($_POST['codigo_colaborador'], $data_pedido, $value['preco'], $caixa['caixa'], $value['id'], $_POST['pagamento'], $lastInsertedId, $value['quantidade']));

        $atualizarCaixaStmt = $db->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ? ");
        $atualizarCaixaStmt->execute(array($value['preco'], $caixa['caixa']));

        $produtoNomeStmt = $db->prepare("SELECT nome FROM `tb_produtos` WHERE  `id` =?");
        $produtoNomeStmt->execute(array($value['id']));
        $produtoNome = $produtoNomeStmt->fetch();

        $printer->text($value['quantidade'] . '-' . str_replace('_', ' ', $value['id']) . " R$" . $value['preco'] . "\n\n");
        $valor_total += $value['preco'];
    }

    $printer->text("Valor Total:R$" . number_format($valor_total, 2, ',', '.') . "\n");
    $printer->text("#Pedido de número " . $lastInsertedId . "\n");

    // Finaliza a impressão e fecha a conexão
    $printer->cut();
    $printer->close();
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e->getMessage() . "\n";
}
?>
