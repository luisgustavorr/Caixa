<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../../MySql.php');
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

date_default_timezone_set('America/Sao_Paulo');

// Função para manipular erros
function errorHandler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
function compararPorPreco($a, $b)
{
    return $a['preco'] - $b['preco'];
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
        $array_retorno = [
            "data" => [],
            "produtos_quitados" => [],
            "resto_da_metade" => [],
            "metade_produto_restante" => [],
            "ret" => []
        ];
        $data_atual = date("Y-m-d h:i:sa");
        $array_retorno["data"] = $data_atual;

        if ($_POST['data_venda'] != '') {
            $data_atual = $_POST['data_venda'];
        }

        unset($_COOKIE['caixa']);
        unset($_COOKIE['last_codigo_colaborador']);
        setcookie("last_codigo_colaborador", $_POST['colaborador'], time() + 20 * 24 * 60 * 60, "/");
        setcookie("caixa", $colab['caixa'], time() + 20 * 24 * 60 * 60, "/");

        $valor_compra_total = 0;

        // Use prepared statements para evitar injeção de SQL

        $vendaStmt = \MySql::conectar()->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`,`quantidade_produto`,`venda_dividida_id`,`troco`) VALUES (NULL, ?,?, ?, ?, ?,?,?,?,?); ");
        $valor_da_parte =  $_POST['valor'];
        $troco = 0;
        if (isset($_POST["valor_troco"])) {
            $troco = str_replace(",", ".", $_POST["valor_troco"]);
        }
        $array_produtos = $_POST['produtos'];
        usort($array_produtos, 'compararPorPreco');
        $metade_quitada = false;
        $atualizar_caixa_sql = ($_POST['pagamento'] == 'Dinheiro') ?   "UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ?, `valor_no_caixa` = `valor_no_caixa` + ? WHERE `tb_caixas`.`caixa` = ?"
            : "UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ?";
        $atualizar_caixa = \MySql::conectar()->prepare($atualizar_caixa_sql);
        $arrayVariaveis = $_POST['pagamento'] == "Dinheiro" ? array($_POST['valor'], $_POST['valor'], trim($colab['caixa'])) : array($_POST['valor'], trim($colab['caixa']));
        $atualizar_caixa->execute($arrayVariaveis);
        foreach ($array_produtos as $key => $value) {
            $quantidade_float = floatval(str_replace(",", ".", $value['quantidade']));
            $valor_produto =  ($value['preco'] * $quantidade_float);
            if ($_POST["segunda_parte"] == "true" and !$metade_quitada) {
                $vendaStmt->execute(array($_POST['colaborador'], $data_atual, floatval(str_replace(',', '.', $_POST['metade_restante_produto'][0])), trim($colab['caixa']), $value['id'], $_POST['pagamento'],  $quantidade_float * 0.5, "9841_", $troco));
                $metade_quitada = true;
                array_push($array_retorno["ret"], floatval(str_replace(',', '.', $_POST['valor_restante'][0])));
                $valor_produto = str_replace(',', '.', $_POST['metade_restante_produto'][0]);

            } else if ($valor_da_parte >= ($value["preco"] * $quantidade_float)) {
                $valor_compra_total += $value['preco'] * $quantidade_float;
                $vendaStmt->execute(array($_POST['colaborador'], $data_atual, $value['preco'] * $quantidade_float, trim($colab['caixa']), $value['id'], $_POST['pagamento'],  $quantidade_float, 0, $troco));
                array_push($array_retorno["produtos_quitados"], $value);
        

            } else if ($valor_da_parte > 0) { //Divide o produto em 2 partes
                $valor_compra_total += $value['preco'] * $quantidade_float;
                $vendaStmt->execute(array($_POST['colaborador'], $data_atual, $valor_da_parte, trim($colab['caixa']), $value['id'], $_POST['pagamento'],  $quantidade_float * 0.5, $colab['caixa'] . "_" . $value['id'], $troco));
                $valor_restante_produto = $valor_da_parte - ($value['preco'] * $quantidade_float);
      
                array_push($array_retorno["metade_produto_restante"], str_replace(".", ",", $valor_restante_produto * -1));
       
            }
            $valor_da_parte = $valor_da_parte -   $valor_produto;

        }
        array_push($array_retorno["ret"], "valor Da Parte 2:" . $valor_da_parte . "\n");

        array_push($array_retorno["resto_da_metade"], str_replace(".", ",", $valor_da_parte * -1));

        print_r(json_encode($array_retorno));
    }
} catch (Exception $e) {
    \ReportError::conectar($e->getMessage() . " na linha " . $e->getLine() . " do arquivo " .  basename(__FILE__), "ahristocrat4@gmail.com");
}
