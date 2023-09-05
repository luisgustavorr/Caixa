<?php 
include('../../MySql.php');
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
date_default_timezone_set('America/Sao_Paulo');

try{
    @$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)
    @$printer->text("FECHAMENTO DE CAIXA\n");
    @$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
    @$printer->text("Dinheiro Informado:".str_replace(',','.',$_POST['dinheiro_informadas'])."\n");
$printer->text("-----------------------------------------\n");

    @$printer->text("Cartão Informado:".str_replace(',','.',$_POST['cartao_informadas'])."\n");
$printer->text("-----------------------------------------\n");

    @$printer->text("Moedas Informadas:".str_replace(',','.',$_POST['moedas_informadas'])."\n");
$printer->text("-----------------------------------------\n");

    @$printer->text("Pix Informado:".str_replace(',','.',$_POST['pix_informadas'])."\n");
$printer->text("-----------------------------------------\n");

    @$printer->text("Sangria Informadas:".str_replace(',','.',$_POST['sangria_informadas'])."\n");
$printer->text("-----------------------------------------\n");


    $equip = \MySql::conectar()->prepare("INSERT INTO `tb_fechamento` (`id`, `dinheiro`, `cartao`, `moeda`, `pix`, `sangria`, `data`,`caixa`) VALUES (NULL, ?, ?, ?, ?, ?, ?,?)");
    $equip->execute(array(str_replace(',','.',$_POST['dinheiro_informadas']),str_replace(',','.',$_POST['cartao_informadas']),str_replace(',','.',$_POST['moedas_informadas']),str_replace(',','.',$_POST['pix_informadas']),str_replace(',','.',$_POST['sangria_informadas']),date("Y-m-d"),$_POST["caixa_alvo"]));
    $equip = $equip->fetch();
    echo date("Y-m-d");
    $printer->cut();
$printer->close();
}catch(Exception $e){
    echo 'ERRO: Preencha todos os valores!'.$e;
}


    ?>