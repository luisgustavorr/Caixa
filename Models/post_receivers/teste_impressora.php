<?php 
include('../../MySql.php');
date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . '/../../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;


$caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
$caixa->execute(array(trim('Loja Várzea')));
$caixa = $caixa ->fetch();
  try{
    $connector = new WindowsPrintConnector(dest:$caixa['impressora']);

    $printer = new Printer($connector);
$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)

$printer->text("SANGRIA DE CAIXA\n");
$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
$printer->text("Data: " . date("d/m/Y H:i:s") . "\n"); // Adicione a data e hora da sangria
$printer->text("Teste de Impressão"); // Adicione a data e hora da sangria

$printer->text("Se você está lendo isso é porque funcionou!"); // Adicione a data e hora da sangria

// Escreve o rodapé da mensagem
$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)

$drawerCommand = "\x1B\x70\x00\x19\xFA";

// Envie o comando para a impressora
$connector->write($drawerCommand);
// Finaliza a impressão e fecha a conexão
$printer->cut();
$printer->close();

    

  
  

}catch(Exception $e){   
    echo 'Falha ao imprimir: '.$e->getMessage();

}

?>