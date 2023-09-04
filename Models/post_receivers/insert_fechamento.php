<?php 
include('../../MySql.php');
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
date_default_timezone_set('America/Sao_Paulo');

try{


    $equip = \MySql::conectar()->prepare("INSERT INTO `tb_fechamento` (`id`, `dinheiro`, `cartao`, `moeda`, `pix`, `sangria`, `data`,`caixa`) VALUES (NULL, ?, ?, ?, ?, ?, ?,?)");
    $equip->execute(array(str_replace(',','.',$_POST['dinheiro_informadas']),str_replace(',','.',$_POST['cartao_informadas']),str_replace(',','.',$_POST['moedas_informadas']),str_replace(',','.',$_POST['pix_informadas']),str_replace(',','.',$_POST['sangria_informadas']),date("Y-m-d"),$_POST["caixa_alvo"]));
    $equip = $equip->fetch();
    echo date("Y-m-d");
}catch(Exception $e){
    echo 'ERRO: Preencha todos os valores!'.$e;
}


    ?>