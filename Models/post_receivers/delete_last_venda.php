<?php 
include('../../MySql.php');

    $equip = \MySql::conectar()->prepare("DELETE FROM `tb_vendas` WHERE `pedido_id` = 0 AND `caixa` = ?
    ORDER BY `tb_vendas`.`id` DESC
    LIMIT 1;");
    $equip->execute(array($_COOKIE["last_codigo_colaborador"]));
?>