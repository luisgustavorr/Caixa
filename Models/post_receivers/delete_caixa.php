<?php 
include('../../MySql.php');

    $apagar_anterior = \MySql::conectar()->prepare("DELETE FROM `tb_caixas` WHERE caixa = ?");
    $apagar_anterior->execute(array($_POST['caixa']));
?>