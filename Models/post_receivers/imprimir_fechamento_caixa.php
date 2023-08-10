<?php 
require __DIR__ . '/../../vendor/autoload.php';
print_r($_POST);
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

// Conecta à impressora (substitua "printer_device" pelo caminho ou nome da porta da impressora)
$connector = new WindowsPrintConnector(dest:"TM-T20X");

$printer = new Printer($connector);
// Configura o modo de impressão (tamanho da fonte, negrito, etc.)
$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)

// Escreve o cabeçalho do fechamento de caixa
$printer->text("FECHAMENTO DE CAIXA\n");
$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
$printer->text("Data: " . date("d/m/Y H:i:s") . "\n"); // Adicione a data e hora do fechamento
$funcionario = $_POST['funcionario'];
$printer->text("Funcionário: " . $funcionario  . "\n"); // Adicione a data e hora do fechamento

// Escreve os valores informados pelo funcionário
$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)
$printer->text("Valores Apurados\n");
$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
$printer->text("Troco Inicial ".$_POST['troco_inicial']."\n");
$printer->text("Total Vendas ".$_POST['total_vendas']."\n");
$printer->text("Troco Final ".$_POST['troco_final']."\n");
$printer->text("Total Apurado ".$_POST['total_apurado']."\n");
$printer->text("Total Informado ".$_POST['total_informado']."\n");
if($_POST['diferenca'] < 0 ){
$printer->setEmphasis(true); // Desativa o modo de enfatizar (negrito)

}
$printer->text("Diferença ".$_POST['diferenca']."\n");

// Escreve o campo de assinatura
$printer->text("\nAssinatura _______________________\n");

// Finaliza a impressão e fecha a conexão
$printer->cut();
$printer->close();
?>