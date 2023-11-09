<?php
include('../../MySql.php');
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

date_default_timezone_set('America/Sao_Paulo');

// Preparar consultas SQL
$colabQuery = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
$caixaQuery = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
$produtoQuery = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `id` = ?");
$vendaQuery = \MySql::conectar()->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`) VALUES (NULL, ?, ?, ?, ?, ?, ?);");
$atualizarCaixaQuery = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ?");
$atualizarCaixaDinheiroQuery = \MySql::conectar()->prepare("UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ?, `valor_no_caixa` = `valor_no_caixa` + ? WHERE `tb_caixas`.`caixa` = ?");

// Verificação e operações
$colabQuery->execute(array($_POST['colaborador']));
$colab = $colabQuery->fetch();

if (!empty($colab)) {
    // Limpar cookies
    unset($_COOKIE['caixa']);
    unset($_COOKIE['last_codigo_colaborador']);
    
    // Definir novos cookies
    setcookie("last_codigo_colaborador", $_POST['colaborador'], time() + 20 * 24 * 60 * 60);
    setcookie("caixa", $colab['caixa'], time() + 20 * 24 * 60 * 60);
    
    $data_atual = date("Y-m-d h:i:sa");
    $valor_compra_total = 0;

    foreach ($_POST['produtos'] as $key => $value) {
        $valor_compra_total += ($value['preco'] * $value['quantidade']);
        
        $produtoQuery->execute(array($value['id']));
        $produto = $produtoQuery->fetch();
        
        $vendaQuery->execute(array(
            $_POST['colaborador'],
            $data_atual,
            $value['preco'] * $value['quantidade'],
            trim($colab['caixa']),
            $value['id'],
            $_POST['pagamento']
        ));

        if ($_POST['pagamento'] == 'Dinheiro') {
            $atualizarCaixaDinheiroQuery->execute(array($_POST['valor'],$_POST['valor'], trim($colab['caixa'])));
        } else {
            $atualizarCaixaQuery->execute(array($_POST['valor'], trim($colab['caixa'])));
        }
    }
} else {
    echo 'Código de vendedor inválido';
}
?>
