<?php 
include('../../MySql.php');

    $equip = \MySql::conectar()->prepare("SELECT * FROM `tb_equipamentos` WHERE `caixa` =?");
    $equip->execute(array($_POST['caixa']));
    $equip = $equip->fetch();
    print_r(json_encode($equip));
    ?>