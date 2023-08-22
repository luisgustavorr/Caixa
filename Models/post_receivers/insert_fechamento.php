<?php 
include('../../MySql.php');
date_default_timezone_set('America/Sao_Paulo');
try{
    $equip = \MySql::conectar()->prepare("INSERT INTO `tb_fechamento` (`id`, `dinheiro`, `cartao`, `moeda`, `pix`, `sangria`, `data`) VALUES (NULL, ?, ?, ?, ?, ?, ?)");
    $equip->execute(array(str_replace(',','.',$_POST['dinheiro_informadas']),str_replace(',','.',$_POST['cartao_informadas']),str_replace(',','.',$_POST['moedas_informadas']),str_replace(',','.',$_POST['pix_informadas']),str_replace(',','.',$_POST['sangria_informadas']),date("Y-m-d")));
    $equip = $equip->fetch();
}catch(Exception $e){
    echo 'ERRO: Preencha todos os valores!';
}


    ?>