<?php 
include('../../MySql.php');


    $equip = \MySql::conectar()->prepare("INSERT INTO `tb_caixas` (`id`, `caixa`, `valor_atual`, `troco_inicial`) VALUES (NULL, ?, ?, ?);");
    $equip->execute(array($_POST['nome_caixa'],$_POST['troco_inicial'],$_POST['troco_inicial']));

    ?>