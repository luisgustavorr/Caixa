<?php 
include('../../MySql.php');

    $apagar_anterior = \MySql::conectar()->prepare("DELETE FROM `tb_equipamentos` WHERE caixa = ?");
    $apagar_anterior->execute(array($_POST['caixa']));
    $equip = \MySql::conectar()->prepare("INSERT INTO `tb_equipamentos` (`id`, `caixa`, `impressora`, `velocidade_balanca`, `porta_balanca`) VALUES (NULL, ?, ?, ?, ?);");
    $equip->execute(array($_POST['caixa'],$_POST['impressora'],$_POST['freq_balanca'],$_POST['porta_balanca']));
    $equip = $equip->fetch();
    ?>