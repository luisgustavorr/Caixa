<?php 
include('../../MySql.php');
date_default_timezone_set('America/Sao_Paulo');
$let =$_POST['path'].'vendor\\autoload.php';
require_once $let;
require __DIR__ . '/../../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

$user = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores`  WHERE `codigo` = ?");
$user->execute(array($_POST['colaborador']));
$user = $user->fetch();
print_r(empty($user));
if(!empty($user)){

  try{
    $connector = new WindowsPrintConnector(dest:"TM-T20X");

    $printer = new Printer($connector);
$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)

$printer->text("SANGRIA DE CAIXA\n");
$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
$printer->text("Data: " . date("d/m/Y H:i:s") . "\n"); // Adicione a data e hora da sangria

$valorSangria = $_POST['valor_sangria']; // Valor da sangria (substitua pelo valor correto)
$motivoSangria = $_POST['mensagem']; // Motivo da sangria (substitua pelo motivo correto)
$novo_valor = $_POST['valor']; 

$printer->text("Valor Sangria: R$" . number_format($valorSangria, 2, ',', '.') . "\n");
$printer->text("Novo valor em Caixa: R$" . $novo_valor . "\n");
$printer->text("Motivo: " . $motivoSangria . "\n");

// Escreve o rodapé da mensagem
$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)
$printer->text("Assinatura _______________________\n");
$drawerCommand = "\x1B\x70\x00\x19\xFA";

// Envie o comando para a impressora
$connector->write($drawerCommand);
// Finaliza a impressão e fecha a conexão
$printer->cut();
$printer->close();
    $sangria = \MySql::conectar()->prepare("INSERT INTO `tb_sangrias` (`id`, `colaborador`, `caixa`, `mensagem`, `valor`, `data`) VALUES (NULL,?,?,?,?,?)");
    $sangria->execute(array($_POST['colaborador'],$_POST['caixa'],$_POST['mensagem'],$_POST['valor_sangria'],date("Y-m-d h:i:sa")));
    

  
  

}catch(Exception $e){   
    echo 'Falha ao imprimir: '.$e->getMessage();

}
}


?>