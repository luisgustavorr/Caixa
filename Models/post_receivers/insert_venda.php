<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../MySql.php');
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

date_default_timezone_set('America/Sao_Paulo');

// Função para manipular erros
function errorHandler($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

// Registra a função de manipulação de erros
set_error_handler('errorHandler');

try {
    // Simulação de um erro
    // trigger_error("Este é um erro de teste.", E_USER_ERROR);

    // Restante do código...
    $colab = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores` WHERE codigo = ?");
    $colab->execute(array($_POST['colaborador']));
    $colab = $colab->fetch();

    if (empty($colab)) {
        echo 'Código de vendedor inválido';
    } else {
        $data_atual = date("Y-m-d h:i:sa");
        unset($_COOKIE['caixa']);
        unset($_COOKIE['last_codigo_colaborador']);
        setcookie("last_codigo_colaborador", $_POST['colaborador'], time() + 20 * 24 * 60 * 60,"/");
        setcookie("caixa", $colab['caixa'], time() + 20 * 24 * 60 * 60,"/");
    
        $valor_compra_total = 0;
    
        // Use prepared statements para evitar injeção de SQL
        $produtoStmt = \MySql::conectar()->prepare("SELECT * FROM `tb_produtos` WHERE `id` = ?");
        $vendaStmt = \MySql::conectar()->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`) VALUES (NULL, ?, ?, ?, ?,?,?); ");
    
        foreach ($_POST['produtos'] as $key => $value) {
            $quantidade_float = floatval(str_replace(",",".",$value['quantidade']));
       
            $valor_compra_total += $value['preco'] * $quantidade_float;
    
            $produtoStmt->execute(array($value['id']));
            $produto = $produtoStmt->fetch();
    
            $vendaStmt->execute(array($_POST['colaborador'], $data_atual, $value['preco'] * $quantidade_float, trim($colab['caixa']), $value['id'], $_POST['pagamento']));
    
            $atualizar_caixa_sql = ($_POST['pagamento'] == 'Dinheiro') ?   "UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ?, `valor_no_caixa` = `valor_no_caixa` + ? WHERE `tb_caixas`.`caixa` = ?"
               : "UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ?" 
               ;
    
            $atualizar_caixa = \MySql::conectar()->prepare($atualizar_caixa_sql);
            $arrayVariaveis = $_POST['pagamento'] == "Dinheiro" ? array($_POST['valor'], $_POST['valor'], trim($colab['caixa'])) : array( $_POST['valor'], trim($colab['caixa']));
            $atualizar_caixa->execute($arrayVariaveis);
        }
    }

} catch (Exception $e) {
    \ReportError::conectar($e->getMessage()." na linha ".$e->getLine()." do arquivo ".  basename( __FILE__ ),"ahristocrat4@gmail.com");
}
?>
