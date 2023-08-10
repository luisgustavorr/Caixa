<?php 
include('../../MySql.php');

  $produto = \MySql::conectar()->prepare("UPDATE `tb_pedidos` SET `entregue` = '1' WHERE `tb_pedidos`.`id` = ? ");
  $produto->execute(array($_POST['pedido']));
    echo $_POST['pedido']
?>