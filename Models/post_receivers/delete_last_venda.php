<?php 
include('../../MySql.php');

    $equip = \MySql::conectar()->prepare("DELETE FROM `tb_vendas` WHERE `pedido_id` = 0
    ORDER BY `tb_vendas`.`id` DESC
    LIMIT 1;");
    $equip->execute();
?>