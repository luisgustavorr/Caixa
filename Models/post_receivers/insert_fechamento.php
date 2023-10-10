<?php 
include('../../MySql.php');
require __DIR__ . '/../../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
date_default_timezone_set('America/Sao_Paulo');

try{
    $user = \MySql::conectar()->prepare("SELECT * FROM `tb_colaboradores`  WHERE `codigo` = ?");
$user->execute(array($_POST['codigo_colaborador_informado_fechamento']));
$user = $user->fetch();
    $caixa = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` = ?");
    $caixa->execute(array(trim($user['caixa'])));
    $caixa = $caixa ->fetch();
@$connector = new WindowsPrintConnector(dest:$caixa['impressora']);

@$printer = new Printer($connector);
$printer->setTextSize(2, 2);
$printer->setFont(Printer::FONT_B);
$printer->setLineSpacing(50);
    @$printer->setEmphasis(true); // Ativa o modo de enfatizar (negrito)
    @$printer->text("FECHAMENTO DE CAIXA\n\n");
    @$printer->setEmphasis(false); // Desativa o modo de enfatizar (negrito)
@$printer->text("Data: " . date("d/m/Y H:i:s") . "\n"); // Adicione a data e hora da sangria

    @$printer->text("Dinheiro Informado:".str_replace(',','.',$_POST['dinheiro_informadas'])."\n");
    $printer->text("--------------------------------\n");


    @$printer->text("Cartão Informado:".str_replace(',','.',$_POST['cartao_informadas'])."\n");
    $printer->text("--------------------------------\n");


    @$printer->text("Moedas Informadas:".str_replace(',','.',$_POST['moedas_informadas'])."\n");
    $printer->text("--------------------------------\n");


    @$printer->text("Pix Informado:".str_replace(',','.',$_POST['pix_informadas'])."\n");
    $printer->text("--------------------------------\n");


    @$printer->text("Sangria Informadas:".str_replace(',','.',$_POST['sangria_informadas'])."\n");
    $printer->text("--------------------------------\n");



    $equip = \MySql::conectar()->prepare("INSERT INTO `tb_fechamento` (`id`, `dinheiro`, `cartao`, `moeda`, `pix`, `sangria`, `data`,`caixa`,`colaborador`) VALUES (NULL, ?, ?, ?, ?, ?, ?,?,?)");
    $equip->execute(array(str_replace(',','.',$_POST['dinheiro_informadas']),str_replace(',','.',$_POST['cartao_informadas']),str_replace(',','.',$_POST['moedas_informadas']),str_replace(',','.',$_POST['pix_informadas']),str_replace(',','.',$_POST['sangria_informadas']),date("Y-m-d"),$user["caixa"],$_POST['codigo_colaborador_informado_fechamento']));
    $equip = $equip->fetch();

    $printer->cut();
$printer->close();
}catch(Exception $e){
    echo 'ERRO: Preencha todos os valores!'.$e;
}
