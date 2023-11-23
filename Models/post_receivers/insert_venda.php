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
        $data_atual = date("Y-m-d h:i:sa");
        unset($_COOKIE['caixa']);
        unset($_COOKIE['last_codigo_colaborador']);
        setcookie("last_codigo_colaborador", $_POST['colaborador'], time() + 20 * 24 * 60 * 60, "/");
        setcookie("caixa", $colab['caixa'], time() + 20 * 24 * 60 * 60, "/");

        $valor_compra_total = 0;

        // Use prepared statements para evitar injeção de SQL

        $vendaStmt = \MySql::conectar()->prepare("INSERT INTO `tb_vendas` (`id`, `colaborador`, `data`, `valor`, `caixa`,`produto`,`forma_pagamento`,`venda_dividida_id`) VALUES (NULL, ?, ?, ?, ?,?,?,?); ");
        $valor_da_parte =  $_POST['valor'];
        $array_retorno = [
            "produtos_partidos" => [],
            "produtos_quitados" => [],
            "resto_da_metade" => [],
            "ret" => []
        ];
        $array_produtos = $_POST['produtos'];
        usort($array_produtos, 'compararPorPreco');
        $metade_quitada = false;
        foreach ($array_produtos as $key => $value) {
            array_push($array_retorno["ret"], "valor Da Parte 1:" . $valor_da_parte . "\n");
            $quantidade_float = floatval(str_replace(",", ".", $value['quantidade']));

            if ($_POST["segunda_parte"] == "true" AND !$metade_quitada) {
        
                $apagarMetadeQuitada = \MySql::conectar()->prepare("DELETE FROM `tb_vendas` WHERE `venda_dividida_id` = ?");
                $apagarMetadeQuitada->execute(array($colab['caixa'] . "_" . $value['id']));
                $vendaStmt->execute(array($_POST['colaborador'], $data_atual, $value['preco'] * $quantidade_float, trim($colab['caixa']), $value['id'], $_POST['pagamento'], "9841_"));

                $metade_quitada = true;
            }else if ($valor_da_parte >= $value["preco"] * $quantidade_float) {
                $valor_compra_total += $value['preco'] * $quantidade_float;
                $vendaStmt->execute(array($_POST['colaborador'], $data_atual, $value['preco'] * $quantidade_float, trim($colab['caixa']), $value['id'], $_POST['pagamento'], 0));
                array_push($array_retorno["produtos_quitados"], $value);
            } else if ($valor_da_parte > 0) {


                $quantidade_float = floatval(str_replace(",", ".", $value['quantidade']));
                $valor_compra_total += $value['preco'] * $quantidade_float;
                $vendaStmt->execute(array($_POST['colaborador'], $data_atual, $valor_da_parte, trim($colab['caixa']), $value['id'], $_POST['pagamento'], $colab['caixa'] . "_" . $value['id']));
            }
            $valor_da_parte = $valor_da_parte - $value['preco'] * $quantidade_float;
            array_push($array_retorno["ret"], "valor Da Parte 2:" . $valor_da_parte . "\n");
        }
        $atualizar_caixa_sql = ($_POST['pagamento'] == 'Dinheiro') ?   "UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ?, `valor_no_caixa` = `valor_no_caixa` + ? WHERE `tb_caixas`.`caixa` = ?"
            : "UPDATE `tb_caixas` SET `valor_atual` = `valor_atual` + ? WHERE `tb_caixas`.`caixa` = ?";

        $atualizar_caixa = \MySql::conectar()->prepare($atualizar_caixa_sql);
        $arrayVariaveis = $_POST['pagamento'] == "Dinheiro" ? array($_POST['valor'], $_POST['valor'], trim($colab['caixa'])) : array($_POST['valor'], trim($colab['caixa']));
        $atualizar_caixa->execute($arrayVariaveis);
        array_push($array_retorno["resto_da_metade"], str_replace(".",",",$valor_da_parte * -1));
        print_r(json_encode($array_retorno));
    }
} catch (Exception $e) {
    \ReportError::conectar($e->getMessage() . " na linha " . $e->getLine() . " do arquivo " .  basename(__FILE__), "ahristocrat4@gmail.com");
}
